<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Services\TimeSlotServices\TimeSlotsViewModel;
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
    public function retrieveTimeSlotAjax(Request $request, TimeSlotsViewModel $timeSlotsViewModel): JsonResponse
    {
        if($request->getMethod() === 'POST') {

            $ajaxData = json_decode($request->getContent(), true);       
            
            $startDateString = $ajaxData['startDate'];
            $restaurantId = $ajaxData['restaurantId'];
        
            $timeSlots = $timeSlotsViewModel->build($restaurantId, $startDateString);

            return new JsonResponse($timeSlots, 200);
        }
        
        return new JsonResponse();
    }
}
