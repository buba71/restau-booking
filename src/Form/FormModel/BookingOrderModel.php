<?php

declare(strict_types=1);

namespace App\Form\FormModel;

use App\Entity\Booking;
use App\Entity\OrderLine;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

final class BookingOrderModel
{
    public DateTimeImmutable $registeredAt;
    /**
     * @var Collection<OrderLine> $orderLines
     */
    public Collection $orderLines;
    public Booking $booking;
    public float $amount;
    public string $type;
}