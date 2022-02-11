<?php 

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class TimeSlotFactory
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * @param int $restaurantId
     * @param mixed $data
     * 
     * @return array
     */
    public function create(int $restaurantId, $startDay): array
    {
        // Get first day of week: "2021-12-21T21:00:25.780Z".
        //dd($dateTime);
        $firstDayToDisplay = date('D m Y', strtotime($startDay));
        $dayName = (substr($firstDayToDisplay, 0, 3));
        $dayNumber = intval(date("w", strtotime($dayName)));
        
        $weekDays = $this->resolveCalendarDays($startDay);


        // retrieve restaurant id as function parameter.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);


        /**
         * Refactorer TimeSlotFormater ************************************
         */

        $restaurantTimeSlots = ($restaurant->getTimeSlots())->toArray();

        $timeSlotsWithDate = [];
        foreach($weekDays as $day) {
            foreach($restaurantTimeSlots as $timeSlot) {
                if($timeSlot->hasDate() && ($timeSlot->getDateOfDay())->format('Y-m-d') === $day) {   
                    $restaurantTimeSlots = array_filter($restaurantTimeSlots, fn($item) => $item->getDayOfWeek() !== $timeSlot->getDayOfWeek());    
                    $timeSlotsWithDate[] = $timeSlot;
                } 
            }            
        }

        $timeSlotsWithoutDate = array_filter($restaurantTimeSlots, fn ($item) => !$item->hasDate());        
        $timeSlots = [...$timeSlotsWithoutDate, ...$timeSlotsWithDate];

        // Sort by day of week number (0 => 6).
        usort($timeSlots, function ($a, $b) {
            if($a->getDayOfWeek() === $b->getDayOfWeek()) {
                return 0;
            }
            return ($a->getDayOfWeek() < $b->getDayOfWeek()) ? -1: +1;
        });


        // Resolve timeSlots starting from first day of calendar.
        $array_back = [];

        for($index = 0; $index < count($timeSlots); $index++) {
            if (($timeSlots[$index])->getDayOfWeek() === $dayNumber) {
                // Get the start day of calendar.
                $array_front = array_slice($timeSlots, $index, 1);
            }
            if (($timeSlots[$index])->getDayOfWeek() > $dayNumber) {
                // Get the remaining days.
                array_push($array_front, $timeSlots[$index]);
            } else if (($timeSlots[$index])->getDayOfWeek() < $dayNumber){
                // Get the days before the start day of calendar.
                $array_back[] = $timeSlots[$index];
            }            
        }

        $formattedTimeSlots = [...$array_front, ...$array_back];

        /**
         * Refactorer TimeSlotFormater *************************************
         */




        $timeSlotsViewModel = [];

        foreach($formattedTimeSlots as $timeSlot) {
            $timeSlotsViewModel[] = $this->buildTimeSlot($timeSlot);
        }
        return $timeSlotsViewModel;
    }
 
    /**
     * @param TimeSlot $timeSlot
     * 
     * @return array
     */
    private function buildTimeSlot(TimeSlot $timeSlot): array
    {
        // Restaurant is closed return empty array.
        if ($timeSlot->getServiceStartAt() === null | $timeSlot->getServiceCloseAt() === null | $timeSlot->getIntervalTime() === null) {
            return ['Fermé'];
        }

        $start = $timeSlot->getServiceStartAt();
        $startTime = $start->format('H:i');
        $end = $timeSlot->getServiceCloseAt();
        $interval =  new DateInterval('PT'.$timeSlot->getIntervalTime().'M');

        $timeSlot = [];
        $timeSlot[] = $startTime;

        while($start < $end) {

            $time = $start->add($interval);
            $time = $time->format('H:i');
            
            $timeSlot[] = $time;
        }

        return $timeSlot;
    }

    private function resolveCalendarDays($startDay) 
    {
        // Calendar dates to display.
        $weekDays = [];

        $startDate = new DateTime($startDay);
        for($i = 0; $i < 7; $i++) {
            $weekDays[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }

        return $weekDays;
    }
}
