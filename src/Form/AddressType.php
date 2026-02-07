<?php

namespace App\Form;

use App\Entity\Addresses;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'required' => false,
            ])
            ->add('name')
            ->add('phone')
            ->add('address')
            ->add('postalCode')
            ->add('city')
            ->add('deliveryDefault', null, [
                'data' => false,
                'required' => false,
            ])
            ->add('billingDefault', null, [
                'data' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresses::class,
        ]);
    }
}
