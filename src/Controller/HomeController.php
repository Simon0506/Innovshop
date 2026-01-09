<?php

namespace App\Controller;

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
}
