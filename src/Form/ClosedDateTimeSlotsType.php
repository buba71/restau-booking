<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ClosedDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClosedDateTimeSlotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder    
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de début'
                ]
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de fin'
                ]
            ])
            ->add('reason', TextType::class, [
                'label' => false
            ])            
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info btn-sm'
                ]
            ])
        ;        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            ['data_class' => ClosedDate::class]
        );
    }
}
