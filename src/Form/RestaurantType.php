<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder    
            ->add('name', TextType::class, [
                'label' => 'Nom du restaurant'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone'
            ])
            ->add('speciality', TextType::class, [
                'label' => 'Spécialité de votre restaurant',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Asiatique',
                ]
                
            ])
            ->add('orderEnabled', CheckboxType::class, [    
                'label' => false,  
                'required' => false,          
                'attr' => [
                    'class' => 'pl-0 mb-2',
                    'data-toggle' => 'toggle',
                    'data-onstyle' => 'info',
                    'data-height' => '25',
                    'data-width' => '75'
                ]            
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            ['data_class' => Restaurant::class]
        );
    }
}