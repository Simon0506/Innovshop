<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Orders;
use App\Entity\ProductOptionValue;
use App\Entity\Products;
use App\Entity\ProductVariant;
use App\Entity\User;
use App\Repository\OptionGroupRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/products', name: 'app_products_')]
final class ProductsController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function products(ProductsRepository $productsRepository, OptionGroupRepository $optionGroupRepository, Request $request): Response
    {
        $query = $request->query->get('q', '');
        $request->getSession()->set('previous_url', $request->getUri());
        $sort = $request->query->get('sort');
        $filters = $request->query->all('filters');
        $products = $productsRepository->findFilteredProducts($filters, $sort, $query);
        $optionGroups = $optionGroupRepository->findAll();
        return $this->render('products/all.html.twig', [
            'products' => $products,
            'optionGroups' => $optionGroups,
            'filters' => $filters,
            'sort' => $sort,
            'query' => $query
        ]);
    }

    #[Route('/category/{id}/{slug}', name: 'category')]
    public function categoryProducts(Categories $category, Request $request, OptionGroupRepository $optionGroupRepository, ProductsRepository $productsRepository): Response
    {
        $query = $request->query->get('q', '');
        $request->getSession()->set('previous_url', $request->getUri());
        $sort = $request->query->get('sort');
        $filters = $request->query->all('filters');
        $products = $productsRepository->findFilteredProducts($filters, $sort, $query, $category);
        $optionGroups = $optionGroupRepository->findAll();
        return $this->render('products/category.html.twig', [
            'products' => $products,
            'category' => $category,
            'optionGroups' => $optionGroups,
            'filters' => $filters,
            'sort' => $sort,
            'query' => $query
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addToCart(ProductVariant $pv, Request $request, EntityManagerInterface $em, OrdersRepository $ordersRepository): Response
    {
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

            $cart->addProductVariant($pv, $quantity);
            $em->flush();
        } else {
            $session = $request->getSession();
            $cart = $session->get('cart', []);

            $cart[$pv->getId()] = ($cart[$pv->getId()] ?? 0) + $quantity;

            $session->set('cart', $cart);
        }
        $this->addFlash('cart_open', true);
        $referer = $request->request->get('redirect');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/remove-to-cart/{id}', name: 'remove_to_cart', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function removeToCart(ProductVariant $pv, Request $request, EntityManagerInterface $em, OrdersRepository $ordersRepository): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $quantity = max(1, $quantity);

        if ($this->getUser()) {
            $cart = $ordersRepository->findOneBy([
                'user' => $this->getUser(),
                'status' => Orders::STATUT_CART
            ]);
            $cart->removeProductVariant($pv, $quantity);
            $em->flush();
        } else {
            $session = $request->getSession();
            $cart = $session->get('cart', []);
            $pvId = $pv->getId();

            if (isset($cart[$pvId])) {
                if ($cart[$pvId] > $quantity) {
                    $cart[$pvId] -= $quantity;
                } else {
                    unset($cart[$pvId]);
                }
            }

            $session->set('cart', $cart);
        }

        $this->addFlash('cart_open', true);
        $referer = $request->request->get('redirect');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/{id}/{slug}', name: 'detail', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]+'])]
    public function detail(Products $product, string $slug, Request $request, OrdersRepository $ordersRepository): Response
    {
        if ($slug !== $product->getSlug()) {
            return $this->redirectToRoute('product_detail', [
                'id' => $product->getId(),
                'slug' => $product->getSlug(),
            ], 301);
        }
        $pvs = [];
        $productVariants = $product->getProductVariants();

        $quantitiesInCart = [];

        if ($this->getUser() instanceof User) {
            $cart = $ordersRepository->findOneBy([
                'user' => $this->getUser(),
                'status' => Orders::STATUT_CART
            ]);

            if ($cart) {
                foreach ($cart->getOrderLines() as $line) {
                    if ($line->getProductVariant()) {
                        $quantitiesInCart[$line->getProductVariant()->getId()] =
                            $line->getQuantity();
                    }
                }
            }
        } else {
            $quantitiesInCart = $request->getSession()->get('cart', []);
        }

        $variantAvailable = [];
        foreach ($productVariants as $pv) {
            $alreadyInCart = $quantitiesInCart[$pv->getId()] ?? 0;
            if ($pv->getStock() - $alreadyInCart > 0) {
                $variantAvailable[] = $pv;
            }

            $pvs[] = [
                'id' => $pv->getId(),
                'name' => $pv->getName(),
                'price' => $pv->getPrice(),
                'priceHT' => $pv->getPriceHT(),
                'maxquantity' => max(0, (int) $pv->getStock() - (int) $alreadyInCart)
            ];
        }
        usort($variantAvailable, function (ProductVariant $a, ProductVariant $b) {
            return $a->getPrice() <=> $b->getPrice();
        });
        $firstPV = $variantAvailable[0] ?? null;

        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'pvs' => $pvs,
            'firstPV' => $firstPV,
            'previousUrl' => $request->getSession()->get('previous_url')
        ]);
    }
}
