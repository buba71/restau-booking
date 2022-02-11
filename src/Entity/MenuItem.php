<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MenuItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuItemRepository::class)
 */
final class MenuItem implements Product
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $addedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="menuItems")
     * @ORM\JoinColumn(nullable=false, name="category_id", referencedColumnName="id")
     */
    private Category $category;

    /**
     * @ORM\ManyToMany(targetEntity=Menu::class, mappedBy="menuItems")
     */
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->addedAt = new DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu) 
    {
        if(!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->addMenuItem($this);
        }
    }

    public function removeMenu(Menu $menu) 
    {
        if($this->menus->contains($menu)) {
            $this->menus->remove($menu);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

