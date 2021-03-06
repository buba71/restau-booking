<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
* Class Booking
* 
* @package App\Entity
* 
* @ORM\Entity()
*/
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private int $id;    

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Vous devez sélectionner une date.")
     */
    private DateTime $bookingDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTime $bookingAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message="vous devez saisir un nombre de couverts.")
     */
    private string $coversNumber;

    
    /**
     * @ORM\OneToOne(targetEntity="BookingOrder", mappedBy="booking", cascade={"persist", "remove"})
     */
    private ?BookingOrder $bookingOrder;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="bookings")
     * @ORM\JoinColumn(name="restaurant_id", nullable=false)
     */
    private Restaurant $restaurant; 

    public function __construct()
    {
        $this->bookingAt = new DateTime();        
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBookingDate(): DateTime
    {
        return $this->bookingDate;
    }

    public function setBookingDate($bookingDate)
    {
        $this->bookingDate = $bookingDate;
    }

    public function getBookingAt(): DateTime
    {
        return $this->bookingAt;
    }

    public function getBookingOrder(): ?BookingOrder
    {
        return $this->bookingOrder;
    }

    public function setBookingOrder(BookingOrder $bookingOrder): void
    {
        $this->bookingOrder = $bookingOrder;
    }

    public function getCoversNumber(): string
    {
        return $this->coversNumber;
    }

    public function setCoversNumber($coversNumber)
    {
        $this->coversNumber = $coversNumber;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user) 
    {
        $this->user = $user;
    }
}