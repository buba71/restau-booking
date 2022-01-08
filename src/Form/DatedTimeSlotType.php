<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
            ->add('serviceStartAt', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => false,
                'label' => false
            ])
            ->add('serviceCloseAt', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => false,
                'label' => false
            ])
            ->add('intervalTime', TextType::class, [                
                'required' => false,
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