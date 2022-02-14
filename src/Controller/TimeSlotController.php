<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Services\TimeSlotServices\TimeSlotFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TimeSlotController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }

    #[Route('/time-slots', name: 'time_slots', methods: 'POST')]
    public function retrieveTimeSlotAjax(Request $request, TimeSlotFactory $timeSlotFactory): JsonResponse
    {
        if($request->getMethod() === 'POST') {

            $dateTime = json_decode($request->getContent(), true);
            

            // static restaurant id = 1.
            $restaurantId = 1;
        
            $timeSlots = $timeSlotFactory->create($restaurantId, $dateTime);

            return new JsonResponse($timeSlots, 200);
        }        
    }
}
