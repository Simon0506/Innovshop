<?php

namespace App\Repository;

use App\Entity\ProductVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductVariant>
 */
class ProductVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    public function existsVariantWithSameOptions(ProductVariant $variant): bool
    {
        if ($variant->getProductVariantOptions()->isEmpty()) {
            return false;
        }

        $ids = [];
        foreach ($variant->getProductVariantOptions() as $vo) {
            $ids[] = $vo->getOptionValue()->getId();
        }

        sort($ids);

        $qb = $this->createQueryBuilder('v')
            ->select('v.id')
            ->join('v.productVariantOptions', 'pvo')
            ->join('pvo.optionValue', 'ov')
            ->where('v.product = :product')
            ->andWhere('ov.id IN (:ids)')
            ->groupBy('v.id')
            ->having('COUNT(DISTINCT ov.id) = :count')
            ->setParameter('product', $variant->getProduct())
            ->setParameter('ids', $ids)
            ->setParameter('count', count($ids));

        if (null !== $variant->getId()) {
            $qb->andWhere('v.id != :currentId')
                ->setParameter('currentId', $variant->getId());
        }

        return (bool) $qb->getQuery()->getOneOrNullResult();
    }
}
