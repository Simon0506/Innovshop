<?php

namespace App\Controller;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper
    ) {}

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {

        $id = $request->query->get('id');
        if (!$id) {
            throw $this->createNotFoundException();
        }

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
                $request,
                $user->getId(),
                $user->getEmail()
            );
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre email a bien été vérifié !');

        return $this->redirectToRoute('app_login');
    }
}
