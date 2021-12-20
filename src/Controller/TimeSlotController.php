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
    public function retrieveTimeSlotAjax(Request $request, TimeSlotBuilder $timeSlotBuilder)
    {
        if($request->getMethod() === 'POST') {
            $data = json_decode($request->getContent(), true);
        }

        // TODO  get in the request.
        $restaurantId = 1;
        
        dump($data);
        $result = $timeSlotBuilder->buildTimeSlots($restaurantId);

        return new JsonResponse($result, 200);
    }
}
