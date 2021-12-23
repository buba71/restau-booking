<?php 

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

final class TimeSlotBuilder
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * @param int $restaurantId
     * @param mixed $data
     * 
     * @return array
     */
    public function buildTimeSlots(int $restaurantId, $dateToDisplay): array
    {
        $dayName = (substr($dateToDisplay, 0, 3));
        $dayNumber = (int)(date("w", strtotime($dayName)));
        // Get first day of week: "2021-12-21T21:00:25.780Z".
        



        // retrieve restaurant id as function parameter.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);

        /**
         * Refactorer
         */
        $timeSlotsArray = ($restaurant->getTimeSlots())->toArray();
        $tmp_array = $timeSlotsArray[6];

        //Sort $timeSlotsArray by dayOfWeek(0 => 6).
        unset($timeSlotsArray[6]);
        array_unshift($timeSlotsArray, $tmp_array);
        
        $array_2 = [];

        for($index = 0; $index < count($timeSlotsArray); $index++) {
            if (($timeSlotsArray[$index])->getDayOfWeek() === $dayNumber) {
                $array_1 = array_slice($timeSlotsArray, $index, 1);
            }
            if (($timeSlotsArray[$index])->getDayOfWeek() > $dayNumber) {
                array_push($array_1, $timeSlotsArray[$index]);
            } else if (($timeSlotsArray[$index])->getDayOfWeek() < $dayNumber){
                $array_2[] = $timeSlotsArray[$index];
            }            
        }

        $result = [...$array_1, ...$array_2];
        /**
         * Refactorer
         */




        $timeSlots = [];

        foreach($result as $timeSlot) {
            $timeSlots[] = $this->generateTimeSlot($timeSlot);
        }
        return $timeSlots;
    }
 
    /**
     * @param TimeSlot $timeSlot
     * 
     * @return array
     */
    private function generateTimeSlot(TimeSlot $timeSlot): array
    {
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
}
