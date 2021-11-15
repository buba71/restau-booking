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
            $request->request->has('restaurant_text') && !empty($request->get('restaurant_text')) => $this->nameOrSpecialtiyLoader->getRestaurants($request->get('restaurant_text')),
            $request->request->has('city_text') && !empty($request->get('city_text')) => $this->citiesLoader->getRestaurants($request->get('city_text')),
            default => null
        };
    }
}
