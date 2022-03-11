<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\TimeSlot;
use App\Form\DatedTimeSlotType;
use App\Form\TimeSlotsType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class TimeSlotsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        date_default_timezone_set('Europe/Paris');
    }

    #[Route('/show_time_slots', name: 'show_timeSlots')]
    public function showTimeSlots(): Response
    {
        $restaurant = $this->getUser()->getRestaurant();
        
        $timeSlots = $restaurant->getTimeSlots()->filter(function($element) {
            return !$element->hasDate();
        })->toArray();        
        
        $datedTimeSlots = $restaurant->getTimeSlots()->filter(function($element) {
            return $element->hasDate();
        })->toArray();

        return $this->render('BackOffice/ManagerAccount/Slots/show_time_slots.html.twig', [
            'timeSlots' => $timeSlots,
            'datedTimeSlots' => $datedTimeSlots,
        ]);
    }

    #[Route('/set_time_slots', name: 'set_timeSlots')]
    public function setTimeSlot(Request $request): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        $form = $this->createForm(TimeSlotsType::class, $restaurant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();            

            return $this->redirectToRoute('show_timeSlots');
        }

        return $this->renderForm('BackOffice/ManagerAccount/Slots/set_time_slots.html.twig',  [
            'form' => $form,
        ]);
    } 
    
    #[Route('/update_time_slots', name: 'update_timeSlots')]
    public function updateTimeSlot(Request $request): Response
    {   
        $restaurant = $this->getUser()->getRestaurant();
        
        $weeklyTimeSlots = ($restaurant->getTimeSlots())->filter(function ($element) {
            return !$element->hasDate();
        });

        $datedTimeSlots = ($restaurant->getTimeSlots())->filter(function ($element) {
            return $element->hasDate() && ($element->getStatus() !== TimeSlot::CLOSED_DAY_TIMESLOT_STATUS);
        });

        $nextDatedTimeSlot = new TimeSlot();       


        // Forms to render.
        $timeSlotcollectionForm = $this->createForm(TimeSlotsType::class, $restaurant);
        $datedTimeSlotForm = $this->createForm(DatedTimeSlotType::class, $nextDatedTimeSlot);                    
        
        $timeSlotcollectionForm->handleRequest($request);
        $datedTimeSlotForm->handleRequest($request);

        if ($timeSlotcollectionForm->isSubmitted()) {            
            
            $this->entityManager->flush();

            return $this->redirectToRoute('show_timeSlots');
        }
        
        if ($datedTimeSlotForm->isSubmitted()) {
            
            $dayOfWeek = intval(date('w', strtotime(($nextDatedTimeSlot->getDateOfDay())->format('Y-m-d H:i:s'))));
            $nextDatedTimeSlot->setDayOfWeek($dayOfWeek);

            $restaurant->addTimeSlot($nextDatedTimeSlot);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('show_timeSlots');
        }
        
        return $this->renderForm('BackOffice/ManagerAccount/Slots/update_time_slots.html.twig',  [
            'timeSlotcollectionForm' => $timeSlotcollectionForm,
            'datedTimeSlotForm' => $datedTimeSlotForm,
            'weeklyTimeSlots' => $weeklyTimeSlots->toArray(),
            'datedTimeSlots' => $datedTimeSlots->toArray()
        ]);
    }
}
