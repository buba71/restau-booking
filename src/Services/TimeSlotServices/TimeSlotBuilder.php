<?php 

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

final class TimeSlotBuilder
{
    private EntityManager $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildTimeSlots(int $restaurantId): array
    {
        // retrieve restaurant id as function parameter.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);

        $timeSlots = [];

        foreach($restaurant->getTimeSlots() as $timeSlot ) {
            $timeSlots[] = $this->generateTimeSlot($timeSlot);
        }

        return $timeSlots;
    }

    private function generateTimeSlot(TimeSlot $timeSlot): array
    {
        $start = $timeSlot->getServiceStartAt();
        $startTime = $start->format('H:i');
        $end = $timeSlot->getServiceCloseAt();
        $interval =  $timeSlot->getIntervalTime();

        $timeSlot = [];
        $timeSlot[] = $startTime;

        while($start < $end) {

            $time = $start->add($interval);
            $time = $time->format('H:i');
            
            $timeSlot[] = $time;
        }

        return $timeSlot;
    }

}