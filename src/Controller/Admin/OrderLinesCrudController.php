<?php

namespace App\Controller\Admin;

use App\Entity\OrderLines;
use App\Repository\ProductVariantRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class OrderLinesCrudController extends AbstractCrudController
{
    public function __construct(
        private ProductVariantRepository $productVariantRepository
    ) {
        $this->productVariantRepository = $productVariantRepository;
    }

    public static function getEntityFqcn(): string
    {
        return OrderLines::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addWebpackEncoreEntry('admin');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('productVariant', 'Produit')
                ->setQueryBuilder(function (QueryBuilder $qb) {
                    $qb
                        ->join('entity.product', 'p')
                        ->orderBy('p.title', 'ASC');
                })
                ->setFormTypeOption(
                    'choice_label',
                    function ($variant) {
                        return $variant->getProduct()->getTitle() . ' - ' . $variant->getName();
                    }
                )
                ->setFormTypeOption('choice_attr', function ($variant) {
                    return ['data-price' => $variant->getPrice()];
                })
                ->setFormTypeOption('attr', [
                    'data-product' => 'true',
                ])
                ->setColumns(12),
            NumberField::new('quantity', 'Quantité')
                ->setFormTypeOption('attr', [
                    'data-quantity' => 'true',
                ])
                ->setColumns(12),
            MoneyField::new('unitPrice', 'Prix unitaire')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setFormTypeOption('attr', [
                    'readonly' => true,
                    'data-unit-price' => 'true',
                    'value' => '0.00',
                ])
                ->setColumns(12),
            MoneyField::new('subtotal', 'Sous-total')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setFormTypeOption('attr', [
                    'readonly' => true,
                    'data-subtotal' => 'true',
                    'value' => '0.00',
                ])
                ->setColumns(12),
        ];
    }
}
