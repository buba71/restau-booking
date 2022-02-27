<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Restaurant
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
class Restaurant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable="true")
     */
    private ?string $speciality;

    /**
     * @ORM\OneToMany(targetEntity="TimeSlot", mappedBy="restaurant", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Collection $timeSlots;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="restaurant", cascade={"persist", "remove"})
     */
    private Collection $bookings;

    /**
     * @ORM\OneToMany(targetEntity="ClosedDate", mappedBy="restaurant", cascade={"persist", "remove"})
     */
    private Collection $closedDates;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="restaurant", cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private Collection $menuItems;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="restaurant", cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private Collection $menus;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="restaurant", cascade={"persist"})
     * JoinColumn(name="manager_id", referencedColumnName="id")
     */
    private ?User $user;

    public function __construct()
    {
        $this->timeSlots = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->closedDates = new ArrayCollection();
        $this->menuItems = new ArrayCollection();
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function getBookings()
    { 
        return $this->bookings;
    }

    public function getClosedDates(): Collection
    {
        return $this->closedDates;
    }

    public function addBooking(Booking $booking): void
    {
        if(!$this->bookings->contains($booking)) {
            $booking->setRestaurant($this);
            $this->bookings->add($booking);
        }
    }

    public function removeBooking(Booking $booking): void
    {
        if($this->bookings->contains($booking)) {
            $this->bookings->remove($booking);
        }
    }

    public function addClosedDate(ClosedDate $closedDate): void
    {
        if(!$this->bookings->contains($closedDate)) {
            $closedDate->setRestaurant($this);
            $this->bookings->add($closedDate);
        }
    }

    public function removeClosedDate(Booking $closedDate): void
    {
        if($this->bookings->contains($closedDate)) {
            $this->bookings->remove($closedDate);
        }
    }

    public function addTimeSlot(TimeSlot $timeSlot): void
    {
        if(!$this->timeSlots->contains($timeSlot)) {
            $timeSlot->setRestaurant($this);
            $this->timeSlots->add($timeSlot);
        }
    }

    public function removeTimeSlot(TimeSlot $timeSlot): void
    {
        if($this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->remove($timeSlot);
        }
    }

    public function addMenu(Menu $menu): void
    {
        if(!$this->menus->contains($menu)) {
            $menu->setRestaurant($this);
            $this->menus->add($menu);
        }
    }

    public function removeMenu(Menu $menu): void
    {
        if($this->menus->contains($menu)) {
            $this->menus->remove($menu);
        }
    }

    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenuItem(MenuItem $menuItem): void
    {
        if(!$this->menuItems->contains($menuItem)) {
            $menuItem->setRestaurant($this);
            $this->menuItems->add($menuItem);
        }
    }

    public function removeMenuItem(MenuItem $menuItem): void
    {
        if($this->menuItems->contains($menuItem)) {
            $this->menuItems->remove($menuItem);
        }
    }

    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
