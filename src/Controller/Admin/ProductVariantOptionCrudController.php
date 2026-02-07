<?php

namespace App\Controller\Admin;

use App\Entity\ProductVariantOption;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProductVariantOptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductVariantOption::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('optionValue', 'Valeur d’option'),
        ];
    }
}
