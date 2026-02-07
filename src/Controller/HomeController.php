<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\User;
use App\Form\ProfilType;
use App\Form\UpdatePasswordType;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductsRepository $productsRepository, Request $request): Response
    {
        $products = $productsRepository->findAll();
        $productsUne = [];
        foreach ($products as $product) {
            if ($product->isUne()) {
                $productsUne[] = $product;
            }
        }

        $newProducts = $productsRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        $request->getSession()->set('previous_url', $request->getUri());

        return $this->render('home/index.html.twig', [
            'uneProducts' => $productsUne,
            'newProducts' => $newProducts
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    #[Route('/contact/submit', name: 'app_contact_submit', methods: ['POST'])]
    public function contactSubmit(Request $request, MailerInterface $mailer): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $messageContent = $request->request->get('message');
        $mail = (new Email())
            ->from($email)
            ->to('contact@innovshop.fr')
            ->subject('Nouveau message de contact')
            ->html("<p><strong>Nom :</strong> {$name}</p>
                    <p><strong>Email :</strong> {$email}</p>
                    <p><strong>Message :</strong><br>{$messageContent}</p>");
        $mailer->send($mail);
        $this->addFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
        return $this->redirectToRoute('app_contact');
    }
}
