<?php 

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class TimeSlotsViewModel
{
    public function __construct(private EntityManagerInterface $entityManager, private TimeSlotViewModelResolver $timeSlotViewModel)
    {
        date_default_timezone_set("Europe/Paris");
    }

    /**
     * @param int $restaurantId
     * @param string $today
     * 
     * @return array<string[]>
     * 
     * TODO refactor method naming
     */
    public function build(int $restaurantId, string $today): array
    {     
        // $today is the current day.       
        $currentWeeklyDaysDateTime = $this->resolveCalendarWeeklyDays($today);

        // get restaurant weekly timeSlots.
        $timeSlots = $this->retrieveRestaurantWeeklyTimeSlots($currentWeeklyDaysDateTime, $restaurantId);

        // resolve the php number of current day.
        $todayDateTime = date('D m Y', strtotime($today));
        $todayName = (substr($todayDateTime, 0, 3));
        $todayNumber = intval(date("w", strtotime($todayName)));

        // Reorder timeSlots starting from today from restaurant timeslots list.
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

        $formattedTimeSlots = [...$array_front, ...$array_back];

        // Build View model.
        $timeSlotsViewModel = [];
        foreach($formattedTimeSlots as $timeSlot) {

            $timeSlotsViewModel[] = $this->timeSlotViewModel->resolve($timeSlot);
        }

        return $timeSlotsViewModel;
    }

    /**
     * @param mixed $today
     * 
     * @return array<DateTime>
     */
    private function resolveCalendarWeeklyDays($today): array 
    {
        // Calendar dates to display.
        $currentweeklyDaysDateTime = [];

        $startDate = new DateTime($today);
        for($i = 0; $i < 7; $i++) {
            $currentweeklyDaysDateTime[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }

        return $currentweeklyDaysDateTime;
    }

    /**
     * @param array $currentWeeklyDays
     * @param int $restaurantId
     * 
     * @return array<TimeSlot>
     */
    private function retrieveRestaurantWeeklyTimeSlots(array $currentWeeklyDays, int $restaurantId): array
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