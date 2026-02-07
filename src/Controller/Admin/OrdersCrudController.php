<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrdersCrudController extends AbstractCrudController
{
    public function __construct(
        private UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['date' => 'DESC'])
            ->setFormOptions([
                'attr' => [
                    'data-controller' => 'order',
                    'data-action' => 'change->order#recalculate input->order#recalculate',
                ],
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Utilisateur')
                ->setFormTypeOptions([
                    'choices' => $this->userRepository->findAll(),
                    'choice_label' => function ($user) {
                        $fullname = $user->getFirstName() . ' ' . $user->getName();
                        return $fullname . ' (' . $user->getEmail() . ')';
                    },
                ]),
            TextField::new('numero', 'Numéro de commande')->setDisabled(true),
            CollectionField::new('orderLines', 'Contenu de la commande')
                ->useEntryCrudForm(OrderLinesCrudController::class)
                ->setFormTypeOption('by_reference', false)
                ->setEntryIsComplex(true)
                ->allowAdd()
                ->allowDelete()
                ->onlyOnForms(),
            DateTimeField::new('date', 'Date de commande')->setDisabled(true),
            ChoiceField::new('status', 'Statut de la commande')
                ->setChoices([
                    'Panier' => Orders::STATUT_CART,
                    'Payée' => Orders::STATUT_PAID,
                    'Expédiée' => Orders::STATUT_DELIVERED,
                    'Annulée' => Orders::STATUT_CANCELED,
                ]),
            MoneyField::new('total', 'Total')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setFormTypeOption('attr', [
                    'readonly' => true,
                    'data-order-target' => 'total',
                ]),
        ];
    }
}
