<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Choisir  une image'],
                'constraints' => [
                    new NotBlank(),
                    new Image([
                        'mimeTypes' => ['image/jepg', 'image/png', 'image/jpg'],
                        'maxSize' => '200k',
                        'maxSizeMessage' => "Cette image est trop volumineuse {{ size }}. Max  {{ limit }}"
                    ])
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