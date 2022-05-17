<?php

declare(strict_types=1);

namespace App\Tests\Utils;


final class TimeProvider 
{
    public static function setTime(int $hour, int $minute): \DateTime
    {
        return (new \DateTime('@-0'))->setTime($hour, $minute);
    }
}