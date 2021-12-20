<?php  

declare(strict_types=1);

namespace App\Form;

use App\Entity\TimeSlot;
use DateInterval;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getData();

                $time = (int)$form['intervalTime'];                
                $durationTime = new DateInterval("PT".$time."M");                
                $form['intervalTime'] = $durationTime;               
                
                $event->setData($form);                
            })            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => TimeSlot::class]
        );
    }
}