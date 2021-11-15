<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;

final class ByNameOrSpecialityLoader extends RestaurantLoader
{
    /**
     * @return array<Restaurant>
     */
    public function getRestaurants(mixed $filter): array
    {
        return $this->repository->findBy(['name' => $filter]);
    }
}
