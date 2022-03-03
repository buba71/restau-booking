<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class DatedTimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfDay', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',                
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date',
                ],
            ])
            ->add('serviceStartAtAm', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => true,
                'label' => false
            ])
            ->add('serviceCloseAtAm', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => true,
                'label' => false
            ])
            ->add('serviceStartAtPm', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => true,
                'label' => false
            ])
            ->add('serviceCloseAtPm', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => true,
                'label' => false
            ])
            ->add('intervalTime', ChoiceType::class, [
                'choices'   => [
                    '15 mn' => 15,
                    '30 mn' => 30,
                    '45 mn' => 45,
                    '60 mn' => 60
                ],             
                'required' => true,
                'label' => false
            ])
            ->add('dayOfWeek', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeSlot::class
        ]);
    }
}