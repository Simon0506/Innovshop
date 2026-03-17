<?php

namespace App\Repository;

use App\Entity\Reviews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reviews>
 */
class ReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reviews::class);
    }

    public function getRatingsForProducts(array $products): array
    {
        if (empty($products)) {
            return [];
        }

        $productIds = array_map(fn($row) => $row[0]->getId(), $products);

        $results = $this->createQueryBuilder('r')
            ->select('IDENTITY(r.product) as productId')
            ->addSelect('AVG(r.note) as averageRating')
            ->addSelect('COUNT(r.id) as reviewCount')
            ->where('r.product IN (:products)')
            ->setParameter('products', $productIds)
            ->groupBy('r.product')
            ->getQuery()
            ->getArrayResult();

        $ratings = [];

        foreach ($results as $row) {
            $ratings[$row['productId']] = [
                'averageRating' => round((float)$row['averageRating'], 1),
                'reviewCount' => (int)$row['reviewCount']
            ];
        }

        return $ratings;
    }
}
