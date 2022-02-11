<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\BookingOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderFirstStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            ['dat_class' => BookingOrder::class]
        );
    }
}