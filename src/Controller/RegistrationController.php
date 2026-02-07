<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper
    ) {}

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setRegistrationDate(new \DateTime);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $address = new Addresses();
            $address->setUser($user);
            $address->setFirstName($user->getFirstName());
            $address->setName($user->getName());
            $address->setAddress($user->getAddress());
            $address->setPostalCode($user->getPostalCode());
            $address->setCity($user->getCity());
            $address->setPhone($user->getPhone());
            $address->setActive(true);
            $address->setDeliveryDefault(true);
            $address->setBillingDefault(true);
            $entityManager->persist($address);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $email = (new Email())
                ->from('noreply@innovshop.fr')
                ->to($user->getEmail())
                ->subject('Confirme ton adresse email')
                ->html("
                    <p>Bienvenue !</p>
                    <p>Merci de confirmer ton email en cliquant ici :</p>
                    <p><a href='{$signatureComponents->getSignedUrl()}'>Confirmer mon email</a></p>
                ");

            $mailer->send($email);


            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
