<?php

namespace App\Repository;

use App\Entity\Categories;
use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findFilteredProducts(array $filters, ?string $sort, ?string $query, ?Categories $category = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.productVariants', 'pv');

        if ($category !== null) {
            $qb
                ->join('p.category', 'c')
                ->andWhere('c = :category')
                ->setParameter('category', $category);
        }

        if ($query) {
            $qb->andWhere('p.title LIKE :query OR p.description LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }
        $i = 0;

        foreach ($filters as $optionGroupId => $optionValueIds) {
            if (!is_array($optionValueIds) || empty($optionValueIds)) {
                continue;
            }
            $qb->join('pv.productVariantOptions', 'pvo' . $i)
                ->andWhere('pvo' . $i . '.optionValue IN (:optionValues' . $i . ')')
                ->setParameter('optionValues' . $i, $optionValueIds);
            $i++;
        }

        $qb->groupBy('p.id')
            ->addSelect('MIN(pv.price) AS minPrice');

        match ($sort) {
            'price_asc' => $qb->orderBy('minPrice', 'ASC'),
            'price_desc' => $qb->orderBy('minPrice', 'DESC'),
            'title_asc' => $qb->orderBy('p.title', 'ASC'),
            'title_desc' => $qb->orderBy('p.title', 'DESC'),
            default => null,
        };
        return $qb->getQuery()->getResult();
    }
}
