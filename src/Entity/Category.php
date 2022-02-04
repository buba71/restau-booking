<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * 
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="category")
     */
    private Collection $menuItems;

    public function __construct()
    {
        $this->menuItems = new ArrayCollection();        
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

    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function addMenuItem(MenuItem $menuItem)
    {
        if(!$this->menuItems->contains($menuItem)) {            
            $this->menuItems->add($menuItem);
            $menuItem->setCategory($this);
        }
    }

    public function removeMenuItem(MenuItem $menuItem)
    {
        if($this->menuItems->contains($menuItem)) {
            $this->menuItems->remove($menuItem);
        }

    }

    public function __toString(): string
    {
        return $this->name;
    }

}