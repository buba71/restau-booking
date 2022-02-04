<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Class OrderLine
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
final class OrderLine
{
    private int $id;

    private string $name;

    private float $price;

    private int $quantity;
} 
