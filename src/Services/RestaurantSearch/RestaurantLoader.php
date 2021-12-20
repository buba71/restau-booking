<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;

abstract class RestaurantLoader
{
    public function __construct(protected RestaurantRepository $repository)
    {
    }

    /**
     * @param array<string> $query
     *
     * @return array<Restaurant>|null
     */
    abstract public function getRestaurants(array $query): ?array;
}
