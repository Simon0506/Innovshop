<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' =>'ROLE_USER',
                    'Vendeur' => 'ROLE_SELLER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->setLabel('Role')
                ->allowMultipleChoices(),
            TextField::new('firstName')
                ->setLabel('Prénom'),
            TextField::new('name')
                ->setLabel('Nom'),
            TextField::new('address')
                ->setLabel('Adresse'),
            TextField::new('postal_code')
                ->setLabel('Code postal'),
            TextField::new('city')
                ->setLabel('Ville'),
            TextField::new('phone')
                ->setLabel('Téléphone'),
            
        ];
    }
}
