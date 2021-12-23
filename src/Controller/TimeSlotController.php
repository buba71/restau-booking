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
            $data = json_decode($request->getContent(), true);
            $dateToDisplay = date('D m Y', strtotime($data));

            // static restaurant id = 1.
            $restaurantId = 1;
        
            $timeSlots = $timeSlotBuilder->buildTimeSlots($restaurantId, $dateToDisplay);

            return new JsonResponse($timeSlots, 200);
        }        
    }
}
