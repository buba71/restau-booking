<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\TimeSlotsType;
use App\Services\TimeSlotServices\TimeSlotBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SettingController extends AbstractController
{
    #[Route('/setTimeSlots', name: 'set_timeSlots')]
    public function setTimeSlot(Request $request, EntityManagerInterface $entityManager, TimeSlotBuilder $timeSlotBuilder)
    {
      
      $restaurant = new Restaurant();

      $form = $this->createForm(TimeSlotsType::class, $restaurant);

      $form->handleRequest($request);
      if($form->isSubmitted()) {
        dd($restaurant);
      }

      return $this->renderForm('Backend/setting.html.twig',  [
        'form' => $form,
      ]);
    } 

    #[Route('/show_time_slots/{id}', name: 'show_timeSlots')]
    public function showTimeSlots()
    {

    }
    
    #[Route('/update_timeSlots/{id}', name: 'update_timeSlots')]
    public function updateTimeSlot(Restaurant $restaurant) 
    { 
      $form = $this->createForm(TimeSlotsType::class, $restaurant);

      return $this->renderForm('Backend/update.html.twig',  [
        'form' => $form,
        'restaurant' => $restaurant
      ]);
    }



}
