<?php

declare(strict_types=1);

namespace App\Entity;

use App\entity\BookingOrder;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderLine
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
final class OrderLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="float")
     */
    private float $price;

    /**
     * @ORM\ManyToOne(targetEntity="BookingOrder", inversedBy="orderlines")
     * @ORM\JoinColumn(name="bookingOrder_id", referencedColumnName="id")
     */
    private BookingOrder $bookingOrder;

    /**
     * @ORM\Column(type="float")
     */
    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function getBookingOrder(): BookingOrder
    {
        return $this->bookingOrder;
    }

    public function setBookingOrder(BookingOrder $bookingOrder)
    {
        $this->bookingOrder = $bookingOrder;
    }
}   
