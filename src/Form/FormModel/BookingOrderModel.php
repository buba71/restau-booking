<?php

declare(strict_types=1);

namespace App\Form\FormModel;


use DateTimeImmutable;

final class BookingOrderModel
{
    public DateTimeImmutable $registeredAt;
    public array $orderLines;
    public float $amount;
    public string $type;
}