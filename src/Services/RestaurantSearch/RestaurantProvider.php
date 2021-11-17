<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;
use Symfony\Component\HttpFoundation\Request;

final class RestaurantProvider
{
    public function __construct(private ByCitiesLoader $citiesLoader, private ByNameOrSpecialityLoader $nameOrSpecialtiyLoader)
    {
    }

    /**
     * @return array<Restaurant>|null
     */
    public function serve(Request $request): ?array
    {
        return match (true) {
            $request->query->has('restaurant_text') && !empty($request->query->get('restaurant_text')) => $this->nameOrSpecialtiyLoader->getRestaurants($request->query->get('restaurant_text')),
            $request->query->has('city_text') && !empty($request->query->get('city_text')) => $this->citiesLoader->getRestaurants($request->query->get('city_text')),
            default => null
        };
    }
}
