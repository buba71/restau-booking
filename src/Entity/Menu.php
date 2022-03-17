<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MenuRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
final class Menu implements Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom n'est pas valide.")
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La description n'est pas valide.")
     */
    private string $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="float", nullable="false")
     * @Assert\NotBlank
     * @Assert\Positive(message="Le menu doit contenir un prix valide.")
     */
    private float $price = 0;


    /**
     * @ORM\ManyToMany(targetEntity=MenuItem::class, inversedBy="menus")
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez sélectionner au moin un produit."
     * )
     * 
     */
    private Collection $menuItems;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="menus")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private Restaurant $restaurant;

    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = ucfirst($name);

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    public function addMenuItem(MenuItem $menuItem) 
    {
        if(!$this->menuItems->contains($menuItem)) {
            $this->menuItems->add($menuItem);
            $menuItem->addMenu($this);

        }
    }

    public function removeMenuItem(MenuItem $menuItem) 
    {
        if($this->menuItems->contains($menuItem)) {
            $this->menuItems->remove($menuItem);
        }
    }
}