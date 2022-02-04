<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Menu;
use App\Entity\MenuItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class , [
                'required' => false
            ])
            ->add('menuItems', EntityType::class, [
                'class' => MenuItem::class,
                'choice_label' => function (?MenuItem $menuItem) {
                
                    return $menuItem ? $menuItem->getName() . '(' . $menuItem->getCategory() .')'  : '';
                },
                'choice_value' => 'name',
                'expanded' => true,
                'placeholder' => 'Choisissez une assiette',
                'required' => false,
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [ 'data_class' => Menu::class ]
        );
    }
}