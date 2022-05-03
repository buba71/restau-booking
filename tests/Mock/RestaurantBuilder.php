<?php

declare(strict_types=1);

namespace App\Tests\Mock;

use App\Entity\Restaurant;

final class RestaurantBuilder extends Restaurant
{
    // Allow setting the restaurant Id.
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
}