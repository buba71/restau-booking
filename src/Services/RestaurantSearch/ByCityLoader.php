<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;

final class ByCityLoader extends RestaurantLoader
{
    /**
     * @param array<string> $query
     *
     * @return array<Restaurant>
     */
    public function getRestaurants(array $query): array
    {
        return $this->repository->findBy(['city' => $query], ['name' => 'ASC']);
    }
}
