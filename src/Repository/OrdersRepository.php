<?php

namespace App\Repository;

use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function sortByDate(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->andWhere('o.status != :cart')
            ->andWhere('o.status != :validated')
            ->setParameter('validated', Orders::STATUT_VALIDATED)
            ->setParameter('cart', Orders::STATUT_CART)
            ->setParameter('user', $user)
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestPaidOrderForUser(User $user): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Orders::STATUT_PAID)
            ->orderBy('o.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function groupOrderLinesByProduct(Orders $order): array
    {
        $groupedProducts = [];

        foreach ($order->getOrderLines() as $orderLine) {
            $product = $orderLine->getProductVariant()->getProduct();
            $productId = $product->getId();

            if (!isset($groupedProducts[$productId])) {
                $groupedProducts[$productId] = [
                    'product' => $product,
                    'orderLines' => [],
                    'subtotal' => 0,
                ];
            }

            $groupedProducts[$productId]['orderLines'][] = $orderLine;
            $groupedProducts[$productId]['subtotal'] += $orderLine->getSubtotal();
        }

        return $groupedProducts;
    }
}
