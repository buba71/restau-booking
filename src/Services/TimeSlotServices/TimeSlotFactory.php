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
    public function __construct(private EntityManagerInterface $entityManager)
    {
        date_default_timezone_set("Europe/Paris");
    }

    /**
     * @param int $restaurantId
     * @param string $startDay
     * 
     * @return array[]
     */
    public function create(int $restaurantId, string $startDay): array
    {        
        $weekDays = $this->resolveCalendarWeeklyDays($startDay);

        // retrieve restaurant id as function parameter.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);

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

        // Sort weekly days by day number (0 => Sunday, 1 => Monday, 2 => tuesday, 3 => Wednesday, 4 => Thursday, 5 => Friday, 6 => Saturday).
        
        usort($timeSlots, function ($a, $b) {
            if($a->getDayOfWeek() === $b->getDayOfWeek()) {
                return 0;
            }
            return ($a->getDayOfWeek() < $b->getDayOfWeek()) ? -1: +1;
        });

        $timeSlotsViewModel = [];

        foreach($timeSlots as $timeSlot) {
            $timeSlotsViewModel[] = $this->buildTimeSlot($timeSlot);
        }

        return $timeSlotsViewModel;
    }
 
    /**
     * @param TimeSlot $timeSlot
     * 
     * @return string[]
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

    /**
     * @param mixed $startDay
     * 
     * @return array<string>
     */
    private function resolveCalendarWeeklyDays($startDay): array 
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
