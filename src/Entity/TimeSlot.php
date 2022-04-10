<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    public const CLOSED_DAY_TIMESLOT_STATUS = 7;
    public const CONTINOUS_DAY_TIMESLOT_STATUS = 8;
    public const NORMAL_DAY_TIMESLOT_STATUS = 9;
    public const AM_TIMESLOT_STATUS = 10;
    public const PM_TIMESLOT_STATUS = 11;

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
     * @ORM\Column(type="integer")
     */
    private int $type = self::NORMAL_DAY_TIMESLOT_STATUS;

    /**
     * @ORM\ManyToOne(targetEntity="ClosedDate", inversedBy="timeSlots")
     * @ORM\JoinColumn(name="closedDate_id", referencedColumnName="id")
     */
    private ClosedDate $closedDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    private ?DateTime $serviceStartAtAm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $serviceCloseAtAm;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $serviceStartAtPm;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $serviceCloseAtPm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $intervalTime = null;

    /**
     * @ORM\Column(type="datetime", nullable=true, unique=true)
     */
    private ?DateTime $dateOfDay = null;

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
        $this->status = self::CLOSED_DAY_TIMESLOT_STATUS;
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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type)
    {
        $this->type = $type;
    }

    public function getServiceStartAtAm(): ?DateTime
    {
        return $this->serviceStartAtAm;
    }

    public function setServiceStartAtAm(?DateTime $serviceStartAtAm): void
    {
        $this->serviceStartAtAm = $serviceStartAtAm;
    }

    public function getServiceCloseAtAm(): ?DateTime
    {
        return $this->serviceCloseAtAm;
    }

    public function setServiceCloseAtAm(?DateTime $serviceCloseAtAm): void
    {
        $this->serviceCloseAtAm = $serviceCloseAtAm;
    }

    public function getServiceStartAtPm(): ?DateTime
    {
        return $this->serviceStartAtPm;
    }

    public function setServiceStartAtPm(?DateTime $serviceStartAtPm): void
    {
        $this->serviceStartAtPm = $serviceStartAtPm;
    }

    public function getServiceCloseAtPm(): ?DateTime
    {
        return $this->serviceCloseAtPm;
    }

    public function setServiceCloseAtPm(?DateTime $serviceCloseAtPm): void
    {
        $this->serviceCloseAtPm = $serviceCloseAtPm;
    }

    public function getIntervalTime(): ?int
    {
        return $this->intervalTime;
    }

    public function setIntervalTime(?int $intervalTime): void
    {
        $this->intervalTime = $intervalTime;
    }

    public function getDateOfDay(): ?DateTime
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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->type === 7) {
            if ($this->serviceStartAtAm !== null | $this->serviceCloseAtAm !== null | $this->serviceStartAtPm !== null | $this->serviceCloseAtPm !== null) {
                $context->buildViolation('Laisser tous les champs vides.')
                        ->atPath('type')
                        ->addViolation();                        
            }
        }

        if ($this->type === 8) {
            if ($this->serviceStartAtPm !== null | $this->serviceCloseAtPm !== null) {
                $context->buildViolation('Remplir uniquement les champs du premier service.')
                        ->atPath('type')
                        ->addViolation();
            }
        }

        if ($this->type === 9) {
            if ($this->serviceStartAtAm === null | $this->serviceStartAtAm === null | $this->serviceStartAtPm === null | $this->serviceCloseAtPm === null) {
                $context->buildViolation('Remplir tous les champs horaires.')
                        ->atPath('serviceStartAtAm')
                        ->addViolation();                        
            }
        }
        
        if ($this->type === 10) {
            if ($this->serviceStartAtPm !== null | $this->serviceCloseAtPm !== null) {
                $context->buildViolation('Remplir uniquement les champs du premier service')
                        ->atPath('type')
                        ->addViolation();
            }
        }

        if  ($this->type === 11) {
            if ($this->serviceStartAtAm !== null | $this->serviceCloseAtAm !== null) {
                $context->buildViolation('Remplir uniquement les champs du deuxième service')
                        ->atPath('type')
                        ->addViolation();
            }
        }
    }
}
