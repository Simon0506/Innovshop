<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Orders;
use App\Entity\Products;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'app_products_')]
final class ProductsController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function products(ProductsRepository $productsRepository, Request $request): Response
    {
        $request->getSession()->set('previous_url', $request->getUri());
        $products = $productsRepository->findAll();
        return $this->render('products/all.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/categorie/{id}/{slug}', name: 'category')]
    public function categoryProducts(Categories $category, Request $request): Response
    {
        $request->getSession()->set('previous_url', $request->getUri());
        $products = $category->getProducts();
        return $this->render('products/category.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addToCart(Products $product, Request $request, EntityManagerInterface $em, OrdersRepository $ordersRepository): Response {
        $quantity = (int) $request->request->get('quantity', 1);
        $quantity = max(1, $quantity);

        if ($this->getUser()) {
            $cart = $ordersRepository->findOneBy([
                'user' => $this->getUser(),
                'status' => Orders::STATUT_CART
            ]);

            if (!$cart) {
                $cart = new Orders();
                $cart->setUser($this->getUser());
                $cart->setStatus(Orders::STATUT_CART);
                $cart->setTotal(0);

                $em->persist($cart);
            }
            
            $cart->addProduct($product, $quantity);
            $em->flush();

        } else {
            $session = $request->getSession();
            $cart = $session->get('cart', []);

            $cart[$product->getId()] = ($cart[$product->getId()] ?? 0) + $quantity;

            $session->set('cart', $cart);
        }
        $referer = $request->request->get('redirect');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/remove-to-cart/{id}', name: 'remove_to_cart', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function removeToCart(Products $product, Request $request, EntityManagerInterface $em, OrdersRepository $ordersRepository): Response {
        $quantity = (int) $request->request->get('quantity', 1);
        $quantity = max(1, $quantity);

        if ($this->getUser()) {
            $cart = $ordersRepository->findOneBy([
                'user' => $this->getUser(),
                'status' => Orders::STATUT_CART
            ]);
            $cart->removeProduct($product, $quantity);
            $em->flush();

        } else {
            $session = $request->getSession();
            $cart = $session->get('cart', []);
            $productId = $product->getId();

            if (isset($cart[$productId])) {
                if ($cart[$productId] > $quantity) {
                    $cart[$productId] -= $quantity;
                } else {
                    unset($cart[$productId]);
                }
            }

            $session->set('cart', $cart);
        }

        $referer = $request->request->get('redirect');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/{id}/{slug}', name: 'detail')]
    public function detail(Products $product, Request $request): Response
    {
        $previousUrl = $request->getSession()->get('previous_url');
        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'previousUrl' => $previousUrl
        ]);
    }
}
