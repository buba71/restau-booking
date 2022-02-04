<?php

declare(strict_types=1);

namespace App\Entity;

interface Product
{
    public function getPrice(): float;

    public function setPrice(float $price);

    public function getName(): string;
    
    public function setName(string $name);
}