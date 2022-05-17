<?php

declare(strict_types=1);

namespace App\Services\TimeSlotServices;

use DateInterval;
use DateTime;

/**
 * Retrieve current day from calendar and return an array
 * of days of current week.
 */
final class CalendarConverter
{
    public static function resolveCalendarWeeklyDays(string $currentDay): array
    {
        // Calendar dates to display.
        $currentweeklyDaysDateTime = [];

        $startDate = new DateTime($currentDay);
        for($i = 0; $i < 7; $i++) {
            $currentweeklyDaysDateTime[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }

        return $currentweeklyDaysDateTime;
    }

    /**
     * Resolve the php number of current day.
     */
    public static function getCurrentDayNumber(string $currentDay): int
    {
        $currentDayDateTime = date('D m Y', strtotime($currentDay));
        $currentDayName = (substr($currentDayDateTime, 0, 3));
        
        return intval(date("w", strtotime($currentDayName)));
    }
}