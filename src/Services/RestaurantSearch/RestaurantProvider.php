<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;
use Symfony\Component\HttpFoundation\Request;

final class RestaurantProvider
{
    public function __construct(
        private ByCityLoader $cityLoader,
        private ByNameLoader $nameLoader,
        private BySpecialityLoader $specialityLoader,
        private BySpecialityAndCityLoader $specialityAndCityLoader
    ) {
    }

    /**
     * @return array<Restaurant>|null
     */
    public function serve(Request $request): ?array
    {
        $query = $request->query->all();

        return match (true) {
            !empty($query['restaurant']) => $this->nameLoader->getRestaurants($query),
            !empty($query['speciality']) && !empty($query['city']) => $this->specialityAndCityLoader->getRestaurants($query),
            !empty($query['speciality']) => $this->specialityLoader->getRestaurants($query),
            !empty($query['city']) => $this->cityLoader->getRestaurants($query),
            default => null
        };
    }
}
