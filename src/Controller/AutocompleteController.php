<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AutocompleteController extends AbstractController
{
    public function __construct(private RestaurantRepository $repository)
    {
        
    }

    #[Route('/restaurant_autocomplete', name: 'restaurant_autocomplete')]
    public function restaurantFormAutocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $restaurants = $this->repository->findAllRestaurantsByName($query, 10);

        return new JsonResponse($restaurants, 200);        
    }
    
    #[Route('/speciality_autocomplete', name: 'speciality_autocomplete')]
    public function specialityFormAutocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $specialities = $this->repository->findDistinctSpecialities($query, 10);

        return new JsonResponse($specialities, 200);        
    }

    #[Route('/city_autocomplete', name: 'city_autocomplete')]
    public function cityFormAutocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $cities = $this->repository->findDistinctCities($query, 10);        
        
        return new JsonResponse($cities, 200);
    }
}