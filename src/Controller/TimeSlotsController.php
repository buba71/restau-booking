<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use App\Form\DatedTimeSlotType;
use App\Form\TimeSlotsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
final class TimeSlotsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_time_slots', name: 'show_timeSlots')]
    public function showTimeSlots(): Response
    {
        // Static restaurant id => 1.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => 1]);
        $timeSlots = $restaurant->getTimeSlots();

        return $this->render('BackOffice/ManagerAccount/slots/show_time_slots.html.twig', [
            'timeSlots' => $timeSlots
        ]);
    }

    #[Route('/set_time_slots', name: 'set_timeSlots')]
    public function setTimeSlot(Request $request): Response
    {
        // Static restaurant id => 1.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => 1]);

        $form = $this->createForm(TimeSlotsType::class, $restaurant);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();            

            return $this->render('BackOffice/ManagerAccount/slots/show_time_slots.html.twig', [
                'timeSlots' => $restaurant->getTimeSlots()
            ]);
        }

        return $this->renderForm('BackOffice/ManagerAccount/slots/set_time_slots.html.twig',  [
            'form' => $form,
        ]);
    } 
    
    #[Route('/update_time_slots/{id}', name: 'update_timeSlots')]
    public function updateTimeSlot(Restaurant $restaurant, Request $request) 
    {   
        $datedTimeSlots = ($restaurant->getTimeSlots())->filter(function ($element) {
            return $element->hasDate();
        });
        $nextDatedTimeSlot = new TimeSlot();


        $timeSlotcollectionForm = $this->createForm(TimeSlotsType::class, $restaurant);
        $timeSlotForm = $this->createForm(DatedTimeSlotType::class, $nextDatedTimeSlot);             
        
        $timeSlotcollectionForm->handleRequest($request);
        $timeSlotForm->handleRequest($request);

        if ($timeSlotcollectionForm->isSubmitted()) {
            
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            return $this->render('BackOffice/ManagerAccount/slots/show_time_slots.html.twig', [
                'timeSlots' => $restaurant->getTimeSlots()
            ]);
        }
        
        if ($timeSlotForm->isSubmitted()) {
            
            $dayOfWeek = intval(date('w', strtotime(($nextDatedTimeSlot->getDateOfDay())->format('Y-m-d H:i:s'))));
            $nextDatedTimeSlot->setDayOfWeek($dayOfWeek);

            $restaurant->addTimeSlot($nextDatedTimeSlot);
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();
            
            return $this->render('BackOffice/ManagerAccount/slots/show_time_slots.html.twig', [
                'timeSlots' => $restaurant->getTimeSlots()
            ]);
        }
        
        return $this->renderForm('BackOffice/ManagerAccount/slots/update_time_slots.html.twig',  [
            'timeSlotcollectionForm' => $timeSlotcollectionForm,
            'timeSlotForm' => $timeSlotForm,
            'datedTimeSlots' => $datedTimeSlots->toArray()
        ]);
    }
}
