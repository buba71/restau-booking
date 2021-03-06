<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Booking;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coversNumber', ChoiceType::class, [
                'choices' => array_combine(range(1, 10, 1), range(1, 10, 1) ),        
                'label' => 'Nombre de couverts',
                'placeholder' => 'nombre de couverts',
                'required' => false,
                'attr' => [
                    'class' => 'form-control m-auto w-50'
                ]
            ])
            ->add('bookingDate', HiddenType::class, [
                'required' => true
            ])
            ->add('booking', SubmitType::class, [
                'label' => 'Réserver une table',
                'attr' =>  [ 'class' => 'btn btn-info']
            ])
            ->add('bookingOrder', SubmitType::class,  [
                'label' => 'Réserver et commander',
                'attr' =>  [ 'class' => 'btn btn-info']
            ])
        ;
        
        $builder->get('coversNumber')
                ->addModelTransformer(new CallbackTransformer(
                    function ($coversNumberAsInt) {
                        return intval($coversNumberAsInt);
                    },
                    function ($coversNumberAsString) {
                        return strval($coversNumberAsString);
                    }
                 ))
        ;

        $builder->get('bookingDate')
                ->addModelTransformer(new CallbackTransformer(
                    function (?DateTime $dateTimeToString) {
                        if ($dateTimeToString !== null) {
                            return $dateTimeToString->format('Y-m-d H:i');
                        }                        
                    },
                    function ($stringToDateTime) {
                        if ($stringToDateTime === null)  {
                            return null;
                        }
                        $timeStamp = strtotime($stringToDateTime);
                    
                        return date_create_from_format('Y m d:H:i', date('Y m d:H:i', $timeStamp));
                    }
                 ))
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class
        ]);    
    }
}