<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * class TimeSlot
 * @package App\Entity
 * 
 * @ORM\Entity()
 */
class TimeSlot
{
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4; 
    public const FRIDAY = 5;
    public const SATURDAY = 6;


    /**
     * @ORM\Id 
     * @ORM\GeneratedValue 
     * @ORM\Column(type="integer") 
     * 
     */
    private int $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $hasDate = false;

    /**
     * @ORM\Column(type="integer")
     */
    private int $dayOfWeek;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isClosed = false;

    /**
     * @ORM\ManyToOne(targetEntity="ClosedDate", inversedBy="timeSlots")
     * @ORM\JoinColumn(name="closedDate_id", referencedColumnName="id")
     */
    private ClosedDate $closedDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $serviceStartAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $serviceCloseAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $intervalTime = null;

    /**
     * @ORM\Column(type="datetime", nullable=true, unique=true)
     */
    private DateTime $dateOfDay;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="timeSlots")
     * @ORM\JoinColumn(name="restaurant_id", nullable=false)
     */
    private Restaurant $restaurant;

    public function getId(): int
    {
        return $this->id;
    }

    public function hasDate(): bool
    {
        return $this->hasDate;
    }

    public function getClosedDate(): ClosedDate
    {
        return $this->closedDate;
    }

    public function setClosedDate(ClosedDate $closedDate): void
    {
        $this->isClosed = !$this->isClosed;
        $this->closedDate = $closedDate;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): void
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function getServiceStartAt(): ?DateTime
    {
        return $this->serviceStartAt;
    }

    public function setServiceStartAt(?DateTime $serviceStartAt): void
    {
        $this->serviceStartAt = $serviceStartAt;
    }

    public function getServiceCloseAt(): ?DateTime
    {
        return $this->serviceCloseAt;
    }

    public function setServiceCloseAt(?DateTime $serviceCloseAt): void
    {
        $this->serviceCloseAt = $serviceCloseAt;
    }

    public function getIntervalTime(): ?int
    {
        return $this->intervalTime;
    }

    public function setIntervalTime(?int $intervalTime): void
    {
        $this->intervalTime = $intervalTime;
    }

    public function getDateOfDay(): DateTime
    {
        return $this->dateOfDay;
    }

    public function setDateOfDay(DateTime $dateOfDay): void
    {
        $this->hasDate = !$this->hasDate;
        $this->dateOfDay = $dateOfDay;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }
}
