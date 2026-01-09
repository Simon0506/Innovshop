<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Orders;
use App\Entity\User;
use App\Form\BillingType;
use App\Form\DeliveryType;
use App\Form\ProfilType;
use App\Form\UpdatePasswordType;
use App\Repository\AddressRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
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
        }
        
        // Formulaire infos connexion
        if ($updatePasswordForm->isSubmitted() && $updatePasswordForm->isValid()) {
            $currentPassword = $updatePasswordForm->get('currentPassword')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $updatePasswordForm->get('currentPassword')
                    ->addError(new FormError('Mot de passe actuel incorrect'));
            } else {
                $newPassword = $updatePasswordForm->get('newPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
                $em->flush();
                $this->addFlash('success', 'Mot de passe mis à jour');
                return $this->redirectToRoute('app_user_profil');
            }
        }
        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'profilForm' => $profilForm,
            'updatePasswordForm' => $updatePasswordForm
        ]);
    }

    #[Route('/commandes', name: 'orders')]
    #[IsGranted('ROLE_USER')]
    public function userOrders(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $orders = $user->getOrders();
        $ordersValidated = [];
        $ordersDelivered = [];
        foreach ($orders as $order) {
            $quantity = 0;
            foreach($order->getOrderLines() as $line) {
                $quantity += $line->getQuantity();
            }
            if ($order->getStatus() === Orders::STATUT_VALIDATED) {
                $ordersValidated[] = [
                    'order' => $order,
                    'quantity' => $quantity
                ];
            } else if ($order->getStatus() === Orders::STATUT_DELIVERED) {
                $ordersDelivered[] = [
                    'order' => $order,
                    'quantity' => $quantity
                ];
            }
        }
        return $this->render('user/orders.html.twig', [
            'ordersValidated' => $ordersValidated,
            'ordersDelivered' => $ordersDelivered
        ]);
    }

    #[Route('/panier', name: 'cart')]
    public function cartShow(): Response
    {
        return $this->render('user/cart.html.twig');
    }

    #[Route('/delete-cart', name: 'delete_cart')]
    public function deleteCart(OrdersRepository $ordersRepository, EntityManagerInterface $em, Request $request): Response
    {
        if($this->getUser()) {
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
    public function delivery(OrdersRepository $ordersRepository, Request $request, EntityManagerInterface $em, AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();
        if(!$user instanceof User ) {
            return $this->redirectToRoute('app_login');
        }
        $cart = $ordersRepository->findOneBy([
            'user' => $this->getUser(),
            'status' => Orders::STATUT_CART
        ]);

        if(!$cart) {
            $this->addFlash(
               'error',
               'Une erreur est survenue. Aucun panier trouvé.'
            );
            return $this->redirectToRoute('app_user_cart');
        }
        $deliveryAddress = $addressRepository->findOneBy([
            'user' => $user,
            'deliveryDefault' => true
        ]);
        if(!$deliveryAddress) {
            $deliveryAddress = new Address();
            $deliveryAddress->setDeliveryDefault(true);
        }
        $billingAddress = $addressRepository->findOneBy([
            'user' => $user,
            'billingDefault' => true
        ]);
        if(!$billingAddress) {
            $billingAddress = new Address();
            $billingAddress->setBillingDefault(true);
        }
        $deliveryForm = $this->createForm(DeliveryType::class, $deliveryAddress);
        $billingForm = $this->createForm(BillingType::class, $billingAddress);
        $deliveryForm->handleRequest($request);
        $billingForm->handleRequest($request);
        if ($deliveryForm->isSubmitted() && $deliveryForm->isValid()) {

            $em->persist($deliveryAddress);
            $em->flush();
            $this->addFlash('success', 'Adresse de livraison mise à jour');

            return $this->redirectToRoute('app_user_delivery');
        }
        if ($billingForm->isSubmitted() && $billingForm->isValid()) {
            
            $em->persist($billingAddress);
            $em->flush();
            $this->addFlash('success', 'Adresse de facturation mise à jour');

            return $this->redirectToRoute('app_user_delivery');
        }
        return $this->render('user/delivery.html.twig', [
            'user' => $user,
            'deliveryForm' => $deliveryForm,
            'billingForm' => $billingForm,
            'cart' => $cart
        ]);
    }

    #[Route('/cart-valid', name: 'cart_valid')]
    #[IsGranted('ROLE_USER')]
    public function cartValid(OrdersRepository $ordersRepository, EntityManagerInterface $em): Response
    {
        $cart = $ordersRepository->findOneBy([
            'user' => $this->getUser(),
            'status' => Orders::STATUT_CART
        ]);

        if(!$cart) {
            $this->addFlash(
               'error',
               'Une erreur est survenue. Aucun panier trouvé.'
            );
            return $this->redirectToRoute('app_user_cart');
        }

        foreach($cart->getOrderLines() as $line) {
            $stock = $line->getProduct()->getStock();

            if($line->getQuantity() > $stock && $stock > 0) {
                $oldQuantity = $line->getQuantity();
                $this->addFlash(
                   'error',
                   'ATTENTION ! Le panier a été modifié. Votre panier contenait ' . $oldQuantity() . ' ' . $line->getProduct()->getTitle() . ', il n\'en reste plus que ' . $stock . '.'
                );
                return $this->redirectToRoute('app_user_cart');

            } else if (($line->getProduct() > $stock && $stock == 0) || !$line->getProduct()) {
                $this->addFlash(
                   'error',
                   'ATTENTION ! Un produit n\'est plus disponible. Votre panier a été mis à jour.'
                );
                return $this->redirectToRoute('app_user_cart');
            }
        }
        $cart->setStatus(Orders::STATUT_VALIDATED);
        $em->flush();
        return $this->redirectToRoute('app_user_payment');
    }
}
