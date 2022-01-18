<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coversNumber', ChoiceType::class, [
                'choices' => array_combine(range(1, 10, 1), range(1, 10, 1) ),
                'label' => false,
                'placeholder' => 'nombre de couverts',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('bookingDate', HiddenType::class, [
                'required' => true
            ])
            ->add('booking', SubmitType::class, [
                'label' => 'réservation sans commande',
                'attr' => [
                    'class' => 'btn btn-info'
                ]
                ])
            ->add('bookingOrder', SubmitType::class, [
                'label' => 'réservation avec commande',
                'attr' => [
                    'class' => 'btn btn-info ml-2'
                ]
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
                    function ($dateTimeToString) {
                        // TODO convert datetime to string.
                        return $dateTimeToString;
                    },
                    function ($stringToDateTime) {
                        if ($stringToDateTime === null)  {
                            return null;
                        }
                        $timeStamp = strtotime($stringToDateTime);
                        $date_time = date_create_from_format('Y m d:H:i', date('Y m d:H:i', $timeStamp));
                    
                        return $date_time;
                    }
                 ))
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class
        ]);    
    }
}