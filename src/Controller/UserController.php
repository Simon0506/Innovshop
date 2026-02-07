<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\Reviews;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\BillingType;
use App\Form\DeliveryType;
use App\Form\ProfilType;
use App\Form\ReviewType;
use App\Form\UpdatePasswordType;
use App\Repository\AddressesRepository;
use App\Repository\OrderLinesRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', name: 'app_user_')]
final class UserController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $profilForm = $this->createForm(ProfilType::class, $user);
        $profilForm->handleRequest($request);
        $updatePasswordForm = $this->createForm(UpdatePasswordType::class, $user);
        $updatePasswordForm->handleRequest($request);

        // Formulaire infos personnelles
        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('app_user_profil');
        } else if ($profilForm->isSubmitted()) {
            $this->addFlash('error', 'Erreur dans le formulaire de profil');
        }

        // Formulaire infos connexion
        if ($updatePasswordForm->isSubmitted()) {

            $currentPassword = $updatePasswordForm->get('currentPassword')->getData();

            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $updatePasswordForm->get('currentPassword')
                    ->addError(new FormError('Mot de passe actuel incorrect'));
            }

            if ($updatePasswordForm->isValid()) {
                $newPassword = $updatePasswordForm->get('newPassword')->getData();
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $newPassword)
                );
                $em->flush();

                $this->addFlash('success', 'Mot de passe mis à jour');

                return $this->redirectToRoute('app_user_profil');
            }
        }

        // Formulaire nouvelle adresse
        $addressId = $request->request->get('address_id');

        if ($addressId) {
            $address = $em->getRepository(Addresses::class)->find($addressId);

            if (!$address || $address->getUser() !== $user) {
                throw $this->createAccessDeniedException();
            }
        } else {
            $address = new Addresses();
        }

        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);

        // Adresses par défaut actuelles
        $actualDeliveryAddress = $em->getRepository(Addresses::class)->findOneBy([
            'user' => $user,
            'deliveryDefault' => true
        ]);

        $actualBillingAddress = $em->getRepository(Addresses::class)->findOneBy([
            'user' => $user,
            'billingDefault' => true
        ]);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {

            if ($addressForm->get('deliveryDefault')->getData()) {
                if ($actualDeliveryAddress && $actualDeliveryAddress !== $address) {
                    $actualDeliveryAddress->setDeliveryDefault(false);
                    $em->persist($actualDeliveryAddress);
                }
            }

            if ($addressForm->get('billingDefault')->getData()) {
                if ($actualBillingAddress && $actualBillingAddress !== $address) {
                    $actualBillingAddress->setBillingDefault(false);
                    $em->persist($actualBillingAddress);
                }
            }

            $address->setUser($user);
            $address->setActive(true);
            $em->persist($address);
            $em->flush();

            $this->addFlash(
                'success',
                $addressId ? 'Adresse mise à jour' : 'Nouvelle adresse ajoutée'
            );

            return $this->redirectToRoute('app_user_profil');
        }
        $addresses = $em->getRepository(Addresses::class)->findBy([
            'user' => $user,
            'active' => true
        ]);
        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'profilForm' => $profilForm,
            'updatePasswordForm' => $updatePasswordForm,
            'addressForm' => $addressForm
        ]);
    }

    #[Route('/delete-address/{id}', name: 'delete_address')]
    #[IsGranted('ROLE_USER')]
    public function deleteAddress(Addresses $address, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        if ($address->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $address->setActive(false);
        $em->flush();
        $this->addFlash('success', 'Adresse supprimée');
        return $this->redirectToRoute('app_user_profil');
    }

    #[Route('/orders', name: 'orders')]
    #[IsGranted('ROLE_USER')]
    public function userOrders(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $orders = $em->getRepository(Orders::class)->sortByDate($user);
        $groups = [];
        foreach ($orders as $order) {
            $groups[$order->getId()] = $em->getRepository(Orders::class)->groupOrderLinesByProduct($order);
        }
        return $this->render('user/orders.html.twig', [
            'orders' => $orders,
            'groups' => $groups,
            'statusPaid' => Orders::STATUT_PAID,
            'statusDelivered' => Orders::STATUT_DELIVERED
        ]);
    }

    #[Route('/orders/{id}', name: 'order_detail')]
    #[IsGranted('ROLE_USER')]
    public function orderDetail(Orders $order, EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $groups = $em->getRepository(Orders::class)->groupOrderLinesByProduct($order);
        $reviews = [];
        foreach ($groups as $group) {
            $product = $group['product'];
            $existingReview = $em->getRepository(Reviews::class)->findOneBy([
                'user' => $user,
                'product' => $product
            ]);
            $reviews[$product->getId()] = $existingReview;
        }
        $review = new Reviews();
        $reviewForm = $this->createForm(ReviewType::class, $review);
        $reviewForm->handleRequest($request);
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $product = $em->getRepository(Products::class)->findOneBy([
                'id' => $reviewForm->get('product')->getData()
            ]);
            $existingReview = $em->getRepository(Reviews::class)->findOneBy([
                'user' => $user,
                'product' => $product
            ]);
            if (!$existingReview) {
                $review->setUser($user);
                $review->setProduct($product);
                $review->setDate(new \DateTime());
                $em->persist($review);
                $em->flush();
                $this->addFlash('success', 'Merci pour votre avis !');
            } else {
                $existingReview->setComment($review->getComment());
                $existingReview->setNote($review->getNote());
                $existingReview->setDate(new \DateTime());
                $em->flush();
                $this->addFlash('success', 'Votre avis a été mis à jour !');
            }
            return $this->redirectToRoute('app_user_order_detail', ['id' => $order->getId()]);
        }
        return $this->render('user/order_detail.html.twig', [
            'order' => $order,
            'groups' => $groups,
            'statusPaid' => Orders::STATUT_PAID,
            'statusDelivered' => Orders::STATUT_DELIVERED,
            'reviewForm' => $reviewForm,
            'reviews' => $reviews,
            'productId' => []
        ]);
    }

    #[Route('/orders/cancel/{id}', name: 'order_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function orderCancel(Orders $order, EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        if ($order->getStatus() !== Orders::STATUT_PAID) {
            $this->addFlash('error', 'Seules les commandes payées peuvent être annulées.');
            return $this->redirectToRoute('app_user_orders');
        }
        if (!$this->isCsrfTokenValid('cancel' . $order->getId(), $request->request->get('_token'))) {
            $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $order->setStatus(Orders::STATUT_CANCELED);
        foreach ($order->getOrderLines() as $line) {
            $productVariant = $line->getProductVariant();
            $productVariant->setStock(
                $productVariant->getStock() + $line->getQuantity()
            );
            $em->persist($productVariant);
        }
        $em->flush();
        $this->addFlash('success', 'Commande annulée avec succès.');
        return $this->redirectToRoute('app_user_orders');
    }

    #[Route('/orders/repeat/{id}', name: 'order_repeat', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function orderRepeat(Orders $order, EntityManagerInterface $em, OrdersRepository $ordersRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $cart = $ordersRepository->findOneBy([
            'user' => $this->getUser(),
            'status' => Orders::STATUT_CART
        ]);
        if (!$cart) {
            $cart = new Orders();
            $cart->setUser($user);
            $cart->setStatus(Orders::STATUT_CART);
            $cart->setTotal(0);
            $em->persist($cart);
            $em->flush();
        }
        foreach ($order->getOrderLines() as $line) {
            $productVariant = $line->getProductVariant();
            $quantity = max(1, $line->getQuantity());
            if ($productVariant->getStock() >= $quantity) {
                $cart->addProductVariant($productVariant, $quantity);
            }
        }
        $cart->recalculateTotal();
        $em->flush();
        $this->addFlash('cart_open', true);
        $this->addFlash('success', 'Les articles de la commande ont été ajoutés à votre panier.');
        return $this->redirectToRoute('app_user_orders');
    }

    #[Route('/cart', name: 'cart')]
    public function cartShow(): Response
    {
        return $this->render('user/cart.html.twig');
    }

    #[Route('/delete-cart', name: 'delete_cart')]
    public function deleteCart(OrdersRepository $ordersRepository, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->getUser()) {
            $cart = $ordersRepository->findOneBy([
                'user' => $this->getUser(),
                'status' => Orders::STATUT_CART
            ]);
            if ($cart) {
                $em->remove($cart);
                $em->flush();
            }
        } else {
            $session = $request->getSession();
            $session->remove('cart');
        }

        return $this->redirectToRoute('app_user_cart');
    }

    #[Route('/delivery', name: 'delivery')]
    public function delivery(OrdersRepository $ordersRepository, Request $request, EntityManagerInterface $em, AddressesRepository $addressRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $cart = $ordersRepository->findOneBy([
            'user' => $this->getUser(),
            'status' => Orders::STATUT_CART
        ]);

        if (!$cart) {
            $this->addFlash(
                'error',
                'Une erreur est survenue. Aucun panier trouvé.'
            );
            return $this->redirectToRoute('app_user_cart');
        }

        $defaultDeliveryAddress = $addressRepository->findOneBy([
            'user' => $user,
            'deliveryDefault' => true
        ]);
        $defaultBillingAddress = $addressRepository->findOneBy([
            'user' => $user,
            'billingDefault' => true
        ]);

        $deliveryAddress = new Addresses();
        $billingAddress = new Addresses();

        $deliveryForm = $this->createForm(DeliveryType::class, $deliveryAddress);
        $billingForm = $this->createForm(BillingType::class, $billingAddress);
        $deliveryForm->handleRequest($request);
        $billingForm->handleRequest($request);
        if ($deliveryForm->isSubmitted() && $deliveryForm->isValid()) {
            if ($deliveryForm->get('deliveryDefault')->getData()) {
                if ($defaultDeliveryAddress && $defaultDeliveryAddress !== $deliveryAddress) {
                    $defaultDeliveryAddress->setDeliveryDefault(false);
                    $em->persist($defaultDeliveryAddress);
                }
            }
            $deliveryAddress->setUser($user);
            $deliveryAddress->setActive(true);
            $em->persist($deliveryAddress);
            $em->flush();
            $this->addFlash('success', 'Adresse de livraison ajoutée');

            return $this->redirectToRoute('app_user_delivery');
        }
        if ($billingForm->isSubmitted() && $billingForm->isValid()) {
            if ($billingForm->get('billingDefault')->getData()) {
                if ($defaultBillingAddress && $defaultBillingAddress !== $billingAddress) {
                    $defaultBillingAddress->setBillingDefault(false);
                    $em->persist($defaultBillingAddress);
                }
            }
            $billingAddress->setUser($user);
            $billingAddress->setActive(true);
            $em->persist($billingAddress);
            $em->flush();
            $this->addFlash('success', 'Adresse de facturation ajoutée');

            return $this->redirectToRoute('app_user_delivery');
        }
        return $this->render('user/delivery.html.twig', [
            'user' => $user,
            'defaultDeliveryAddress' => $defaultDeliveryAddress,
            'defaultBillingAddress' => $defaultBillingAddress,
            'deliveryForm' => $deliveryForm,
            'billingForm' => $billingForm,
            'cart' => $cart
        ]);
    }

    #[Route('/cart-valid', name: 'cart_valid')]
    #[IsGranted('ROLE_USER')]
    public function cartValid(OrdersRepository $ordersRepository, EntityManagerInterface $em, Request $request): Response
    {
        $cart = $ordersRepository->findOneBy([
            'user' => $this->getUser(),
            'status' => Orders::STATUT_CART
        ]);

        if (!$cart) {
            $this->addFlash(
                'error',
                'Une erreur est survenue. Aucun panier trouvé.'
            );
            return $this->redirectToRoute('app_user_cart');
        }

        foreach ($cart->getOrderLines() as $line) {
            $stock = $line->getProductVariant()->getStock();
            if ($stock === 0 || !$line->getProductVariant()) {
                $this->addFlash(
                    'error',
                    'Un produit n\'est plus disponible. Votre panier a été mis à jour.'
                );
                return $this->redirectToRoute('app_user_cart');
            } else if ($line->getQuantity() > $stock) {
                $oldQuantity = $line->getQuantity();
                $this->addFlash(
                    'error',
                    'La quantité d\'un produit a été ajustée (de ' . $oldQuantity . ' à ' . $stock . ') en raison de la disponibilité en stock.'
                );
                return $this->redirectToRoute('app_user_cart');
            }
        }

        $deliveryAddressId = $request->request->get('selected_delivery_address');
        $billingAddressId = $request->request->get('selected_billing_address');
        $sameAddress = $request->request->get('sameAsDelivery');
        if ($sameAddress) {
            $billingAddressId = $deliveryAddressId;
        }
        $deliveryAddress = $em->getRepository(Addresses::class)->find($deliveryAddressId);
        $billingAddress = $em->getRepository(Addresses::class)->find($billingAddressId);
        if (!$deliveryAddress || !$billingAddress) {
            $this->addFlash(
                'error',
                'Veuillez sélectionner des adresses de livraison et de facturation valides.'
            );
            return $this->redirectToRoute('app_user_delivery');
        }
        $cart->setDeliveryAddress($deliveryAddress);
        $cart->setBillingAddress($billingAddress);
        $em->flush();
        return $this->redirectToRoute('app_user_payment');
    }

    #[Route('/payment', name: 'payment')]
    #[IsGranted('ROLE_USER')]
    public function payment(OrdersRepository $ordersRepository): Response
    {
        $user = $this->getUser();
        $cart = $ordersRepository->findOneBy([
            'user' => $user,
            'status' => Orders::STATUT_CART
        ]);

        if (!$cart) {
            $this->addFlash('error', 'Aucun panier valide trouvé.');
            return $this->redirectToRoute('app_user_cart');
        }

        // Clé publique pour JS
        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        return $this->render('user/payment.html.twig', [
            'cart' => $cart,
            'stripePublicKey' => $stripePublicKey
        ]);
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createCheckoutSession(OrdersRepository $ordersRepository): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $cart = $ordersRepository->findOneBy([
            'user' => $user,
            'status' => Orders::STATUT_CART
        ]);

        if (!$cart) {
            return $this->json(['error' => 'Aucun panier valide trouvé'], 400);
        }

        $lineItems = [];
        foreach ($cart->getOrderLines() as $line) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($line->getUnitPrice() * 100), // en centimes
                    'product_data' => [
                        'name' => $line->getProductVariant()->getTitle(),
                    ],
                ],
                'quantity' => $line->getQuantity(),
            ];
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'client_reference_id' => (string) $cart->getId(),
            'metadata' => [
                'order_id' => $cart->getId(),
                'user_id' => $user->getId()
            ],
            'success_url' => $this->generateUrl('app_user_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('app_user_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->json(['url' => $checkoutSession->url]);
    }

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(
        Request $request,
        OrdersRepository $ordersRepository,
        EntityManagerInterface $em
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $_ENV['STRIPE_WEBHOOK_SECRET']
            );
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $orderId = $session->metadata->order_id ?? null;
            if (!$orderId) {
                return new Response('Missing order_id', 400);
            }

            $order = $ordersRepository->find($orderId);

            if ($order && $order->getStatus() === Orders::STATUT_CART) {
                $order->setStatus(Orders::STATUT_PAID);
                $order->setNumero(uniqid('IS-' . date('Ymd') . '-'));
                $order->setDate(new \DateTime());

                foreach ($order->getOrderLines() as $line) {
                    $productVariant = $line->getProductVariant();
                    $productVariant->setStock(
                        $productVariant->getStock() - $line->getQuantity()
                    );
                    $em->persist($productVariant);
                }

                $em->flush();
            }
        }

        return new Response('OK');
    }

    #[Route('/payment-success', name: 'payment_success')]
    #[IsGranted('ROLE_USER')]
    public function paymentSuccess(Request $request, OrdersRepository $ordersRepository, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $order = $ordersRepository->findLatestPaidOrderForUser($user);
        if (!$order) {
            $this->addFlash('error', 'Commande introuvable.');
            return $this->redirectToRoute('app_home');
        }

        $html = $this->renderView('invoice/pdf.html.twig', [
            'user' => $user,
            'order' => $order
        ]);
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $mail = new TemplatedEmail();
        $mail->from('no-reply@innovshop.fr');
        $mail->to($user->getEmail());
        $mail->subject('Confirmation de votre commande InnovShop');
        $mail->htmlTemplate('emails/order_confirmation.html.twig');
        $mail->context([
            'user' => $user,
            'order' => $order
        ]);
        $mail->attach($output, 'facture-' . $order->getNumero() . '.pdf', 'application/pdf');
        $mailer->send($mail);

        return $this->render('user/payment_success.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/payment-cancel', name: 'payment_cancel')]
    #[IsGranted('ROLE_USER')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_user_cart');
    }
}
