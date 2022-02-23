<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\ClosedDate;
use App\Entity\TimeSlot;
use App\Form\ClosedDateTimeSlotsType;
use App\Repository\ClosedDateRepository;
use App\Repository\RestaurantRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class ClosedDateController extends AbstractController
{
    public function __construct(private RestaurantRepository $restaurantRepository)
    {
        date_default_timezone_set('Europe/Paris');
    }

    #[Route('/show_closed_date', name: 'show_closed_date')]
    public function show_closed_date(
        ClosedDateRepository $closedDateRepository,
        Request $request
        ): Response {

        $restaurant = $this->restaurantRepository->findOneBy(['user' => $this->getUser()->getId()]);            
        $closedDates = $closedDateRepository->findBy(['restaurant' => $restaurant->getId()]);
        
        $closedDate = new ClosedDate();

        $closedDatesTimeSlotForm = $this->createForm(ClosedDateTimeSlotsType::class, $closedDate);

        $closedDatesTimeSlotForm->handleRequest($request);

        if ($closedDatesTimeSlotForm->isSubmitted() && $closedDatesTimeSlotForm->isValid()) {

            $start = $closedDate->getStartDate();
            $end = $closedDate->getEndDate()->format('Y-m-d');

            
            // control the end date in closedDate entity.
            $end = new DateTime($end); // this !!!
            $end->modify('+ 1 day');

            $interval = new DateInterval('P1D');

            $dateRange = new DatePeriod($start, $interval, $end);

            foreach($dateRange as $date){

                $timeSlot = new TimeSlot();
                $dayOfWeek = intval(date('w', strtotime(($date)->format('Y-m-d H:i:s'))));
                
                $timeSlot->setDateOfDay($date);
                $timeSlot->setDayOfWeek($dayOfWeek);
                $closedDate->addTimeSlot($timeSlot);
                $closedDate->setRestaurant($restaurant);
                $restaurant->addTimeSlot($timeSlot);                
            }
            
            $this->entityManager->persist($closedDate);
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            $this->addFlash('success', 'Période de fermeture enregistrée!');

            return $this->redirectToRoute('show_closed_date', ['id' => $restaurant->getId()]);
        }

        return $this->renderForm('BackOffice/ManagerAccount/ClosedDate/show_closed_dates.html.twig', [
            'form' => $closedDatesTimeSlotForm,
            'closedDates' => $closedDates
        ]);
    }

    #[Route('/delete_closed_date/{id}', name: 'delete_closed_date')]
    public function deleteClosedDate(ClosedDate $closedDate)
    {
        $this->entityManager->remove($closedDate);
        $this->entityManager->flush();

        $this->addFlash('success', 'Période de fermeture supprimée!');

        return $this->redirectToRoute('show_closed_date', ['id' => $closedDate->getRestaurant()->getId()]);
    }
}