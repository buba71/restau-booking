<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\TimeSlot;
use App\Form\DatedTimeSlotType;
use App\Form\TimeSlotsType;
use App\Repository\RestaurantRepository;
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
    public function __construct(private EntityManagerInterface $entityManager, private RestaurantRepository $restaurantRepository)
    {
        date_default_timezone_set('Europe/Paris');
    }

    #[Route('/show_time_slots', name: 'show_timeSlots')]
    public function showTimeSlots(): Response
    {
        $restaurant = $this->restaurantRepository->findOneBy(['user' => $this->getUser()->getId()]);
        
        $timeSlots = $restaurant->getTimeSlots()->filter(function($element) {
            return !$element->isClosed();
        })->toArray();
        
        $datedTimeSlots = array_values(array_filter($timeSlots, function ($element) {
            return $element->hasDate();
        }));

        return $this->render('BackOffice/ManagerAccount/slots/show_time_slots.html.twig', [
            'timeSlots' => $timeSlots,
            'datedTimeSlots' => $datedTimeSlots,
        ]);
    }

    #[Route('/set_time_slots', name: 'set_timeSlots')]
    public function setTimeSlot(Request $request): Response
    {
        // Static restaurant id => 1.
        $restaurant = $this->restaurantRepository->findOneBy(['user' => $this->getUser()->getId()]);

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
    
    #[Route('/update_time_slots', name: 'update_timeSlots')]
    public function updateTimeSlot(Request $request) 
    {   
        $restaurant = $this->restaurantRepository->findOneBy(['user' => $this->getUser()->getId()]);
        
        $datedTimeSlots = ($restaurant->getTimeSlots())->filter(function ($element) {
            return $element->hasDate() && !$element->isClosed();
        });

        $nextDatedTimeSlot = new TimeSlot();       


        // Forms to render.
        $timeSlotcollectionForm = $this->createForm(TimeSlotsType::class, $restaurant);
        $datedTimeSlotForm = $this->createForm(DatedTimeSlotType::class, $nextDatedTimeSlot);                    
        
        $timeSlotcollectionForm->handleRequest($request);
        $datedTimeSlotForm->handleRequest($request);

        if ($timeSlotcollectionForm->isSubmitted()) {
            
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            return $this->redirectToRoute('show_timeSlots');
        }
        
        if ($datedTimeSlotForm->isSubmitted()) {
            
            $dayOfWeek = intval(date('w', strtotime(($nextDatedTimeSlot->getDateOfDay())->format('Y-m-d H:i:s'))));
            $nextDatedTimeSlot->setDayOfWeek($dayOfWeek);

            $restaurant->addTimeSlot($nextDatedTimeSlot);
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('show_timeSlots');
        }
        
        return $this->renderForm('BackOffice/ManagerAccount/slots/update_time_slots.html.twig',  [
            'timeSlotcollectionForm' => $timeSlotcollectionForm,
            'datedTimeSlotForm' => $datedTimeSlotForm,
            'datedTimeSlots' => $datedTimeSlots->toArray()
        ]);
    }
}
