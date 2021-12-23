<?php  

declare(strict_types=1);

namespace App\Form;

use App\Entity\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        $resolver->setDefaults(
            ['data_class' => TimeSlot::class]
        );
    }
}