<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class BookingOrder
 *
 * @package App\Entity
 *
 * @ORM\Entity()
 */
final class BookingOrder
{
    public const TAKE_AWAY = "takeAway";
    public const ON_SPOT = "onSpot";
    
    public const ORDER_PENDING = 0;
    public const ORDER_VALIDATED = 1;
    public const ORDER_PROCESSING = 2;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("order:read")
     */
    private int $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("order:read")
     */
    private ?string $comment;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("order:read")
     */
    private DateTimeImmutable $registeredAt;

    /**
     * @ORM\Column(type="float")
     * @Groups("order:read")
     */
    private float $amount;

    /**
     * @ORM\Column(type="integer")
     * @Groups("order:read")
     */
    private int $status = self::ORDER_PENDING;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("order:read")
     */
    private string $type;

    /**
     * @ORM\OneToOne(targetEntity="Booking", inversedBy="bookingOrder", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     */
    private ?Booking $booking;

    /**
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="bookingOrder", cascade={"persist", "remove"})
     */
    private Collection $orderlines;

    public function __construct() 
    {
        $this->registeredAt = new DateTimeImmutable();
        $this->orderlines = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type) 
    {
        $this->type = $type;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function addOrderline(Orderline $orderline)
    {
        if (!$this->orderlines->contains($orderline)) {
            $this->orderlines->add($orderline);
            $orderline->setBookingOrder($this);
        }
    }

    public function removeOrderLine(OrderLine $orderLine)
    {
        if ($this->orderlines->contains($orderLine)) {
            $this->orderlines->removeElement($orderLine);
        }
    }

    public function getOrderLines(): Collection
    {
        return $this->orderlines;
    }
}