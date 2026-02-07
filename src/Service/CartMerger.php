<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Products;
use App\Repository\OrdersRepository;
use App\Repository\ProductVariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartMerger
{
    private $session;
    public function __construct(
        private OrdersRepository $ordersRepository,
        private ProductVariantRepository $productVariantRepository,
        private EntityManagerInterface $em,
        RequestStack $requestStack
    ) {
        $this->session = $requestStack->getSession();
    }

    public function mergeSessionCartToUserCart($user): void
    {
        $sessionCart = $this->session->get('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $cart = $this->ordersRepository->findOneBy([
            'user' => $user,
            'status' => Orders::STATUT_CART
        ]);

        if (!$cart) {
            $cart = new Orders();
            $cart->setUser($user);
            $cart->setStatus(Orders::STATUT_CART);
            $cart->setTotal(0);
            $this->em->persist($cart);
        }

        foreach ($sessionCart as $pvId => $quantity) {
            $pv = $this->productVariantRepository->find($pvId);
            if (!$pv) continue;

            $cart->addProductVariant($pv, $quantity);
        }

        $this->em->flush();
        $this->session->remove('cart');
    }
}
