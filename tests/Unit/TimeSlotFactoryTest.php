<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\TimeSlotServices\DefaultTimeSlotsFactory;
use App\Services\TimeSlotServices\TimeSlotsViewModel;
use App\Services\TimeSlotServices\TimeSlotViewModelResolver;
use App\Tests\Mock\RestaurantBuilder;
use App\Tests\Utils\CallPrivateMethod;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

final class TimeSlotFactoryTest extends TestCase
{
    use CallPrivateMethod;

    private $entityManager;
    private $restaurantRepository;
    private TimeSlotsViewModel $timeSlotsViewModel;
    private DefaultTimeSlotsFactory $timeSlotsFactory;

    protected function setUp(): void
    {
        $this->timeSlotsFactory = new DefaultTimeSlotsFactory();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->restaurantRepository = $this->createMock(ObjectRepository::class);
        $this->timeSlotsViewModel = new TimeSlotsViewModel($this->entityManager, new TimeSlotViewModelResolver());        
    }

    public function testIfResolveWeeklyDaysFromStartDay()
    {
        $this->callMethod($this->timeSlotsViewModel, 'resolveCalendarWeeklyDays', ['2022-04-21T22:50:12']);
        // TODO assert.
    }  
    
    public function testIfRetrieveRestaurantWeeklyTimeslots()
    {
        $restaurant = new RestaurantBuilder();
        $restaurant->setId(1);
        $this->timeSlotsFactory->createTimeSlots($restaurant);

        $this->restaurantRepository
            ->expects($this->any())
            ->method('findOneBy')
            ->willReturn($restaurant);
        
        $this->entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->restaurantRepository);

        $weeklyDays = ['2022-04-21', '2022-04-22', '2022-04-23', '2022-04-24', '2022-04-25', '2022-04-26',  '2022-04-27'];

        $this->callMethod($this->timeSlotsViewModel, 'retrieveRestaurantWeeklyTimeSlots', [$weeklyDays, 1]);
        // TODO assert.
    }
} 