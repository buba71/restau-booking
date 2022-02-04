<?php

declare(strict_types=1);

namespace App\entity;

use App\Entity\Booking;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookingOrder
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=BookingOrderRepository::class)
 */
final class BookingOrder
{
    private const TAKE_AWAY = "takeAway";
    private const ON_SPOT = "onSpot";
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $comment;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $registeredAt;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @ORM\Column(type="strinng", length=255)
     */
    private string $type;

    /**
     * @ORM\OneToOne(targetEntity="Booking")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     */
    private ?Booking $booking;

    /**
     * Relation...................................
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
        return $this->string;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
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
            $orderline->setBookinOrder($this);
        }
    }

    public function removeOrderLine(OrderLine $orderLine)
    {
        if ($this->orderlines->contains($orderLine)) {
            $this->orderlines->remove($orderLine);
        }
    }

    public function getOrderLines(): Collection
    {
        return $this->orderlines;
    }
}