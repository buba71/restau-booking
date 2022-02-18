<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ClosedDateRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClosedDateRepository::class)
 */
class ClosedDate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private String $reason = 'Aucun';
    
    /**
     * @ORM\OneToMany(targetEntity="TimeSlot", mappedBy="closedDate", cascade={"persist", "remove"})
     */
    private Collection $timeSlots;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="closedDates")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private Restaurant $restaurant;

    public function __construct()
    {
        $this->timeSlots = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate =  $endDate;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function addTimeSlot(TimeSlot $timeSlot): void
    {
        if (!$this->timeSlots->contains($timeSlot)) {
            $timeSlot->setClosedDate($this);
            $this->timeSlots->add($timeSlot);
        }
    }

    public function removeTimeSlot(TimeSlot $timeSlot): void
    {
        if ($this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->remove($timeSlot);
        }
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