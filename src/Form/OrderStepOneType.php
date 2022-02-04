<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderStepOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'A emporter' => 'takeAway',
                    'Sur place' => 'onSpot'
                ],
                'label' => false,
                'label_attr' => ['class' => 'radio-inline mr-5'],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('card', SubmitType::class, [
                'label' => 'Voir la carte',
                'attr' => [
                    'class' => 'btn btn-info w-100'
                ]
            ])
            ->add('menu', SubmitType::class, [
                'label' => 'Voir nos menus',
                'attr' => [
                    'class' => 'btn btn-info w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
    }
}