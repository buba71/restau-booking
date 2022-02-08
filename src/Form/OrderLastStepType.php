<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\BookingOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderLastStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider votre commande',
                'attr' => [
                    'class' => 'btn btn-info mr-2'
                ] 
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => BookingOrder::class]
        );
    }
}