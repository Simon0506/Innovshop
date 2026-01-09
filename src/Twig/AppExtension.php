<?php

namespace App\Twig;

use App\Entity\Orders;
use App\Repository\CategoriesRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    

    public function __construct(
        private CategoriesRepository $categoriesRepository,
        private OrdersRepository $ordersRepository,
        private ProductsRepository $productsRepository,
        private RequestStack $requestStack,
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function getGlobals(): array
    {
        $user = $this->security->getUser();
        $cart = null;
        $cartQuantity = 0;

        if ($user) {
            $cart = $this->ordersRepository->findOneBy([
                'user' => $user,
                'status' => Orders::STATUT_CART
            ]);
            if ($cart) {
                foreach ($cart->getOrderLines() as $line) {
                    $stock = (int) $line->getProduct()->getStock();
                    if($stock < (int) $line->getQuantity()) {
                        $line->setQuantity($stock);
                        $this->em->flush();
                    }
                    $cartQuantity += $line->getQuantity();
                }
            }
        } else {
            $session = $this->requestStack->getSession();
            $cart = [
                'orderLines' => [],
                'total' => 0
            ];
            $rawCart = $session->get('cart', []);
            foreach ($rawCart as $productId => $quantity) {
                $product = $this->productsRepository->find($productId);
                if (!$product) continue;
                if($product->getStock() < $quantity) {
                    $quantity = $product->getStock();
                }
                $subtotal = $product->getPrice() * $quantity;
                $cart['orderLines'][] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                
                $cart['total'] += $subtotal;
                $cartQuantity += $quantity;
            }
        }

        return [
            'categories' => $this->categoriesRepository->findAll(),
            'cart' => $cart,
            'cartQuantity' => $cartQuantity
        ];
    }
}