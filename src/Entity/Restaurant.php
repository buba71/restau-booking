<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    protected int $id = 1;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir un nom valide.")
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageFilePath = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir une adresse valide.")
     */
    protected string $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir un code postal valide.")
     */
    protected string $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir une ville valide.")
     */
    protected string $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir un numéro de téléphone valide.")
     */
    protected string $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable="true")
     */
    protected ?string $speciality;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $orderEnabled = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $bookingEnabled = false;

    /**
     * @ORM\OneToMany(targetEntity="TimeSlot", mappedBy="restaurant", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    protected Collection $timeSlots;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="restaurant", cascade={"persist", "remove"})
     */
    protected Collection $bookings;

    /**
     * @ORM\OneToMany(targetEntity="ClosedDate", mappedBy="restaurant", cascade={"persist", "remove"})
     */
    protected Collection $closedDates;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="restaurant", cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected Collection $menuItems;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="restaurant", cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected Collection $menus;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="restaurant", cascade={"persist"})
     * JoinColumn(name="manager_id", referencedColumnName="id")
     */
    protected ?User $user;

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

    public function getImageFilePath(): ?string
    {
        return $this->imageFilePath;
    }

    public function setImageFilePath(?string $imageFilePath)
    {
        $this->imageFilePath = $imageFilePath;
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

    public function orderEnabled(): bool
    {
        return $this->orderEnabled;
    }

    public function setOrderEnabled(bool $orderEnabled): void
    {
        $this->orderEnabled = $orderEnabled;
    }

    public function bookingEnabled(): bool
    {
        return $this->bookingEnabled;
    }

    public function setBookingEnabled(bool $bookingEnabled): void
    {
        $this->bookingEnabled = $bookingEnabled;
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
        dump($timeSlot);
        if($this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->removeElement($timeSlot);
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
