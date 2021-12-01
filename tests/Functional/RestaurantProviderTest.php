<?php

declare(strict_types=1);

namespace tests\Functional;

use App\Entity\Restaurant;
use App\Services\RestaurantSearch\ByCityLoader;
use App\Services\RestaurantSearch\ByNameLoader;
use App\Services\RestaurantSearch\BySpecialityAndCityLoader;
use App\Services\RestaurantSearch\BySpecialityLoader;
use App\Services\RestaurantSearch\RestaurantProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class RestaurantProviderTest extends KernelTestCase
{
    private ByCityLoader $cityLoader;
    private ByNameLoader $nameLoader;
    private BySpecialityLoader $specialityLoader;
    private BySpecialityAndCityLoader $specialityAndCityLoader;
    

    protected function setUp(): void
    {
        $kernel = self::bootkernel();
        $entitymanager = $kernel->getContainer()->get('doctrine')->getManager();
        $restaurantRepository = $entitymanager->getRepository(Restaurant::class);


        $this->cityLoader = new ByCityLoader($restaurantRepository);
        $this->nameLoader = new ByNameLoader($restaurantRepository);
        $this->specialityLoader = new BySpecialityLoader($restaurantRepository);
        $this->specialityAndCityLoader = new BySpecialityAndCityLoader($restaurantRepository);   
    }

    /**
     * @dataProvider provideRequest
     */
    public function testIfReturnRightRestaurantList(
        string $primary_queryAttribut,
        string $primary_queryValue,
        string $secondary_queryAttribut,
        string $secondary_queryValue,
        int $expectedValue
    ): void {

        $request = new Request();

        $request->query->set($primary_queryAttribut, $primary_queryValue);
        $request->query->set($secondary_queryAttribut, $secondary_queryValue);

        $provider = new RestaurantProvider(
            $this->cityLoader,
            $this->nameLoader,
            $this->specialityLoader,
            $this->specialityAndCityLoader
        );

        $restaurants = $provider->serve($request);

        static::assertCount($expectedValue, $restaurants);        
    }

    /**
     * @return iterable
     */
    public function provideRequest(): iterable
    {
        yield 'Load restaurant by name' => ['restaurant', 'Le Bistrot 1', '', '', 1];
        yield 'Load restaurant by speciality' => ['speciality', 'Cuisine française', '', '', 9];
        yield 'Load restaurants by city' => ['city', 'Paris', '', '', 11];
        yield 'Load restaurants by city and speciality' => ['speciality', 'Cuisine Asiatique', 'city', 'Paris', 11];
    }
}