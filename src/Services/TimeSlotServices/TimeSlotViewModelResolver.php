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
        date_default_timezone_set("Europe/Paris");
    }

    public function resolve(TimeSlot $timeSlot)
    {
        return match($timeSlot->getType()) {
            TimeSlot::CLOSED_DAY_TIMESLOT_STATUS => $this->buildTimeSlotWhenClosed(),
            TimeSlot::CONTINOUS_DAY_TIMESLOT_STATUS => $this->buildTimeSlotWhenContinuous($timeSlot),
            TimeSlot::NORMAL_DAY_TIMESLOT_STATUS => $this->buildTimeSlotWhenNormal($timeSlot),
            TimeSlot::AM_TIMESLOT_STATUS => $this->buildTimeSlotWhenAm($timeSlot),
            TimeSlot::PM_TIMESLOT_STATUS =>  $this->buildTimeSlotWhenPm($timeSlot),
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

    private function buildTimeSlotWhenClosed(): array
    {
        return ['Fermé'];
    }

    private function buildTimeSlotWhenContinuous(TimeSlot $timeSlot): array
    {
        return $this->buildTimeSlotViewModel(
            $timeSlot->getServiceStartAtAm(),
            $timeSlot->getServiceCloseAtAm(),
            $timeSlot->getIntervalTime()
        );
    }

    private function buildTimeSlotWhenNormal(TimeSlot $timeSlot): array
    {
        $amTimeSlot = $this->buildTimeSlotViewModel(
            $timeSlot->getServiceStartAtAm(),
            $timeSlot->getServiceCloseAtAm(),
            $timeSlot->getIntervalTime()
        );
        $pmTimeSlot = $this->buildTimeSlotViewModel(
            $timeSlot->getServiceStartAtPm(),
            $timeSlot->getServiceCloseAtPm(),
            $timeSlot->getIntervalTime()
        );

        // Space represent middle day separator on viewModel.
        return array_merge($amTimeSlot, [' '], $pmTimeSlot);
    }

    private function buildTimeSlotWhenAm(TimeSlot $timeSlot): array
    {
        return $this->buildTimeSlotViewModel(
            $timeSlot->getServiceStartAtAm(),
            $timeSlot->getServiceCloseAtAm(),
            $timeSlot->getIntervalTime()
        );
    }

    private function buildTimeSlotWhenPm(TimeSlot $timeSlot): array
    {
        return $this->buildTimeSlotViewModel(
            $timeSlot->getServiceStartAtPm(),
            $timeSlot->getServiceCloseAtPm(),
            $timeSlot->getIntervalTime()
        );
    }
}
