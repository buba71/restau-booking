<?php

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;

final class WeeklyTimeSlots 
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
        date_default_timezone_set("Europe/Paris");
    }


    /**
     * @param array $currentWeeklyDays
     * @param int $restaurantId
     * 
     * @return array<TimeSlot>
     */
    public function retrieveRestaurantWeeklyTimeSlots(array $currentWeeklyDays, int $restaurantId): array
    {
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]); 

        $restaurantTimeSlots = $restaurant->getTimeSlots()->toArray();

        $timeSlotsWithDate = [];

        foreach($currentWeeklyDays as $day) {

            foreach($restaurantTimeSlots as $timeSlot) {

                if($timeSlot->hasDate() && ($timeSlot->getDateOfDay())->format('Y-m-d') === $day) {

                    $restaurantTimeSlots = array_filter(
                        $restaurantTimeSlots,
                        fn($item) => $item->getDayOfWeek() !== $timeSlot->getDayOfWeek()
                    );    
                    $timeSlotsWithDate[] = $timeSlot;
                } 
            }            
        }

        $timeSlotsWithoutDate = array_filter($restaurantTimeSlots, fn($item) => !$item->hasDate());        
        $timeSlots = [...$timeSlotsWithoutDate, ...$timeSlotsWithDate];

        // Sort weekly days by day number (0 => Sunday, 1 => Monday, 2 => tuesday, 3 => Wednesday, 4 => Thursday, 5 => Friday, 6 => Saturday).        
        usort($timeSlots, function ($a, $b) {            
            return $a->getDayOfWeek() <=> $b->getDayOfWeek();
        });

        return $timeSlots;
    }
}