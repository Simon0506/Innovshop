<?php

namespace App\Controller\Admin;

use App\Entity\OptionValue;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OptionValueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OptionValue::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Valeur d\'option')
            ->setEntityLabelInPlural('Valeurs d\'options');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('optionGroup', 'Type'),
            TextField::new('value', 'Valeur'),
        ];
    }
}
