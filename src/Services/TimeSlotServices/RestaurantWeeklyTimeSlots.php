<?php

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Retrieve the restaurant timeslots of current week.
 */
final class RestaurantWeeklyTimeSlots 
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        date_default_timezone_set("Europe/Paris");
    }


    /**
     * @param array $currentWeeklyDays
     * @param int $restaurantId
     * 
     * @return array<TimeSlot>
     */
    public function retrieveRestaurantWeeklyTimeSlots(int $restaurantId, array $currentWeeklyDays, int $todayNumber): array
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

        return $this->formatTimeSlotsToCalendar($timeSlots, $todayNumber);
    }

    /**
     * Reorder timeSlots starting from today from restaurant timeslots list.
     * @param array $timeSlots
     * @param int $todayNumber
     * 
     * @return array
     */
    private function formatTimeSlotsToCalendar(array $timeSlots, int $todayNumber): array
    {
        $array_front = [];
        $array_back = [];

        for($index = 0; $index < count($timeSlots); $index++) {
            if ($timeSlots[$index]->getDayOfWeek() === $todayNumber) {
                // Get the start day of calendar.
                $array_front = array_slice($timeSlots, $index, 1);
            }
            if ($timeSlots[$index]->getDayOfWeek() > $todayNumber) {
                // Get the remaining days.
                array_push($array_front, $timeSlots[$index]);
            } else if ($timeSlots[$index]->getDayOfWeek() < $todayNumber){
                // Get the days before the start day of calendar.
                $array_back[] = $timeSlots[$index];
            }            
        }

        return [...$array_front, ...$array_back];
    }
}