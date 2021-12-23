<?php

declare(strict_types=1);

namespace App\Form\FormModel;

final class TimeSlotModel
{
    public int $id;
    public bool $hasDate;
    public int $dayOfWeek;
    public string $serviceStartAt;
    public string $serviceCloseAt;
    public string $intervalTime;
    
}