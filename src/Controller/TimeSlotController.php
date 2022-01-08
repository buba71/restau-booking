<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Services\TimeSlotServices\TimeSlotBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TimeSlotController extends AbstractController
{
    #[Route('/timeSlots', name: 'timeSlots', methods: 'POST')]
    public function retrieveTimeSlotAjax(Request $request, TimeSlotBuilder $timeSlotBuilder): JsonResponse
    {
        if($request->getMethod() === 'POST') {
            $dateTime = json_decode($request->getContent(), true);
            

            // static restaurant id = 1.
            $restaurantId = 1;
        
            $timeSlots = $timeSlotBuilder->buildTimeSlots($restaurantId, $dateTime);

            return new JsonResponse($timeSlots, 200);
        }        
    }
}
