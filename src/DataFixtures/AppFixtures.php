<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Services\TimeSlotServices\DefaultTimeSlotsFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function __construct(private DefaultTimeSlotsFactory $defaultTimeSlotsFactory) {}

    public function load(ObjectManager $manager): void
    {
        for ($index = 1; $index < 10; $index++) {
            $restaurant = new Restaurant();
            $restaurant->setName(sprintf('Le Bistrot %d', $index));
            $restaurant->setAddress('rue du général Degaules');
            $restaurant->setCity(sprintf('Paris %d', $index));
            $restaurant->setZipcode('75000');
            $restaurant->setPhone('0651424874');
            $restaurant->setSpeciality('Cuisine française');
            $restaurant->setImageFilePath('/uploads/images/restaurant-6253550f59495.png');
            $this->defaultTimeSlotsFactory->createTimeSlots($restaurant);

            $manager->persist($restaurant);
        }

        for ($index = 10; $index < 20; $index++) {
            $restaurant = new Restaurant();
            $restaurant->setName(sprintf('Le Restau %d', $index));
            $restaurant->setAddress('rue du général Degaules');
            $restaurant->setCity(sprintf('Lyon %d', $index));
            $restaurant->setZipcode('69000');
            $restaurant->setPhone('0651424874');
            $restaurant->setSpeciality('Cuisine Asiatique');
            $restaurant->setImageFilePath('/uploads/images/restaurant-6253550f59495.png');

            $manager->persist($restaurant);
        }

        for ($index = 19; $index < 30; $index++) {
            $restaurant = new Restaurant();
            $restaurant->setName(sprintf('le Restau %d', $index));
            $restaurant->setAddress('rue du général Degaules');
            $restaurant->setCity('Paris');
            $restaurant->setZipcode('69000');
            $restaurant->setPhone('0651424874');
            $restaurant->setSpeciality('Cuisine Asiatique');
            $restaurant->setImageFilePath('/uploads/images/restaurant-6253550f59495.png');

            $manager->persist($restaurant);
        }

        $manager->flush();
    }
}
