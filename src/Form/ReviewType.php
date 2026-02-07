<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Reviews;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', null, [
                'label' => 'Votre avis',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Partagez votre expérience...'
                ]
            ])
            ->add('note', HiddenType::class, [
                'required' => true,
            ])
            ->add('product', HiddenType::class, [
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}
