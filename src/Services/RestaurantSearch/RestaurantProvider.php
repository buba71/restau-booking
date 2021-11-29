<?php

declare(strict_types=1);

namespace App\Services\RestaurantSearch;

use App\Entity\Restaurant;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class RestaurantProvider
{
    public function __construct(
        private ByCityLoader $cityLoader,
        private ByNameLoader $nameLoader,
        private BySpecialityLoader $specialityLoader,
        private BySpecialityAndCityLoader $specialityAndCityLoader,
        private PaginatorInterface $paginator
    ) {
    }

    /**
     * @return PaginationInterface<SlidingPagination>|null
     */
    public function serve(Request $request): ?PaginationInterface
    {
        $query = $request->query->all();

        return match (true) {
            !empty($query['restaurant']) => $this->withPagination(
                $this->nameLoader->getRestaurants($query),
                $request
            ),
            !empty($query['speciality']) && ! empty($query['city']) => $this->withPagination(
                $this->specialityAndCityLoader->getRestaurants($query),
                $request
            ),
            !empty($query['speciality']) => $this->withPagination(
                $this->specialityLoader->getRestaurants($query),
                $request
            ),
            !empty($query['city']) => $this->withPagination(
                $this->cityLoader->getRestaurants($query),
                $request
            ),
            default => null
        };
    }

    /**
     * @param array<Restaurant> $repositoryData
     *
     * @return PaginationInterface<SlidingPagination>
     */
    public function withPagination(array $repositoryData, Request $request): PaginationInterface
    {
        return $this->paginator->paginate(
            $repositoryData,
            $request->query->getInt('page', 1),
            20
        );
    }
}
