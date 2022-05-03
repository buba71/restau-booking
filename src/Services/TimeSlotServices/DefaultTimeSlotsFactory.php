<?php

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use DateTime;

final class DefaultTimeSlotsFactory
{
    public const AM_TIME = [
        'start' => [
            'hour' => 10,
            'min' =>  00
        ],
        'close' => [
            'hour' => 14,
            'min' => 00
        ]
    ];

    public const PM_TIME = [
        'start' => [
            'hour' => 18,
            'min' => 00
        ],
        'close' => [
            'hour' => 22,
            'min' => 00
        ]
    ];

    private DateTime $startAtAm;
    private DateTime $closeAtAm;
    private DateTime $startAtPm;
    private DateTime $closeAtPm;

    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');

        // TODO => create private factory method.
        $this->startAtAm = new DateTime('@-0');
        $this->startAtAm->setTime(self::AM_TIME['start']['hour'], self::AM_TIME['start']['min']);
        $this->closeAtAm = new DateTime('@-0');
        $this->closeAtAm->setTime(self::AM_TIME['close']['hour'], self::AM_TIME['close']['min']);
        $this->startAtPm = new DateTime('@-0');
        $this->startAtPm->setTime(self::PM_TIME['start']['hour'], self::PM_TIME['start']['min']);
        $this->closeAtPm = new DateTime('@-0');
        $this->closeAtPm->setTime(self::PM_TIME['close']['hour'], self::PM_TIME['close']['min']);
    }

    public function createTimeSlots(Restaurant $restaurant)
    {
        for ($i = 1; $i < 8; $i++) {

            $timeSlot = new TimeSlot();
            $timeSlot->setServiceStartAtAm($this->startAtAm);
            $timeSlot->setServiceCloseAtAm($this->closeAtAm);
            $timeSlot->setServiceStartAtPm($this->startAtPm);
            $timeSlot->setServiceCloseAtPm($this->closeAtPm);
            $timeSlot->setIntervalTime(30);

            $timeSlot->setDayOfWeek($i);
            $restaurant->addTimeSlot($timeSlot);
        }
        // Set the number of Sunday.
        $restaurant->getTimeSlots()->last()->setDayOfWeek(0);
    }
}