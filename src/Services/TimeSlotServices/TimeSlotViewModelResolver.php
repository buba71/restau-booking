<?php

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\TimeSlot;
use DateInterval;
use DateTime;

final class TimeSlotViewModelResolver
{
    public function __construct()
    {
        
    }

    public function resolve(TimeSlot $timeSlot)
    {
        match($timeSlot->getStatus) {
            TimeSlot::CLOSED_DAY_TIMESLOT_STATUS => ['Fermé'],
            TimeSlot::CONTINOUS_DAY_TIMESLOT_STATUS => $this->buildTimeSlotViewModel(),
            TimeSlot::NORMAL_DAY_TIMESLOT_STATUS => $this->buildTimeSlotViewModel(),
            TimeSlot::AM_TIMESLOT_STATUS => $this->buildTimeSlotViewModel(),
            TimeSlot::PM_TIMESLOT_STATUS =>  $this->buildTimeSlotViewModel(),
        };
    }

    /**
     * @param DateTime|null $startTime
     * @param DateTime|null $endTime
     * @param int|null $intervalTime
     * 
     * @return array<string>
     */
    private function buildTimeSlotViewModel(?DateTime $startTime, ?DateTime $endTime, ?int $intervalTime): array
    {
        // if pm service close > 00:00 (ex:18H00 - 02H00), set service close at 23H59.
        if ($startTime > $endTime) {
            $endTime->setTime(23, 59);
        }

        $start = $startTime;
        $startTime = $start->format('H:i');
        $end = $endTime;
        $interval =  new DateInterval('PT'.$intervalTime.'M');

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