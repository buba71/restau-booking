<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\MenuItem;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MenuItemType extends AbstractType
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false
            ])
            ->add('category',  EntityType::class, [
                'class' => Category::class,
                'choices' => $this->categoryRepository->findBy([], ['name' => 'ASC']),
                'placeholder' => 'Choisissez une catégorie',
                'required' => false,
                'label' => false,
                'choice_value' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Ajouter', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info d-inline'
                ]
            ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            ['data_class' => MenuItem::class]
        );
    }
}