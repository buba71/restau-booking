<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;

final class BySpecialityAndCityLoader extends RestaurantLoader
{
    /**
     * @param array<string> $query
     *
     * @return array<Restaurant>|null
     */
    public function getRestaurants(array $query): ?array
    {
        return $this->repository->findBySpecialityAndCity($query);
    }
}
