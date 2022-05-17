<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\TimeSlot;
use App\Services\TimeSlotServices\CalendarConverter;
use App\Services\TimeSlotServices\DefaultTimeSlotsFactory;
use App\Services\TimeSlotServices\TimeSlotsViewModel;
use App\Services\TimeSlotServices\TimeSlotViewModelResolver;
use App\Services\TimeSlotServices\RestaurantWeeklyTimeSlots;
use App\Tests\Mock\RestaurantBuilder;
use App\Tests\Utils\CallPrivateMethod;
use App\Tests\Utils\TimeProvider;
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
    private RestaurantWeeklyTimeSlots $weeklyTimeSlots;
    private TimeSlotViewModelResolver $timeSlotViewModelResolver;

    protected function setUp(): void
    {
        $this->timeSlotsFactory = new DefaultTimeSlotsFactory();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->restaurantRepository = $this->createMock(ObjectRepository::class);
        $this->timeSlotViewModelResolver = new TimeSlotViewModelResolver(); 
        $this->weeklyTimeSlots = new RestaurantWeeklyTimeSlots($this->entityManager);   
    }

    public function testIfResolveWeeklyDaysFromStartDay(): void
    {
        $weeklyDays = CalendarConverter::resolveCalendarWeeklyDays('2022-04-21T22:50:12');

        $expected =  [
            "2022-04-21",
            "2022-04-22",
            "2022-04-23",
            "2022-04-24",
            "2022-04-25",
            "2022-04-26",
            "2022-04-27",
        ];

        static::assertSame($expected, $weeklyDays);
        static::assertCount(7, $weeklyDays);
    }  
    
    public function testIfRetrieveRestaurantWeeklyTimeslots(): void
    {
        $currentDay = '2022-04-21T22:50:12';
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

        $weeklyDays = CalendarConverter::resolveCalendarWeeklyDays($currentDay);
        $currentDayNumber = CalendarConverter::getCurrentDayNumber($currentDay);

        $restaurantTimeSlots = $this->weeklyTimeSlots->retrieveRestaurantWeeklyTimeSlots(1,$weeklyDays, $currentDayNumber);
        
        static::assertCount(7, $restaurantTimeSlots);
    }

    /**
     * @dataProvider provideTimeSlots
     */
    public function testTimeSlotDependingOnDayType(
        int $type,
        ?\DateTime $startAtAm,
        ?\DateTime $closeAtAm,
        ?\DateTime $startAtPm,
        ?\DateTime $closeAtPm,
        array $expected, 
    ):void {

        $timeSlot = new TimeSlot();
        $timeSlot->setType($type);
        $timeSlot->setServiceStartAtAm($startAtAm);
        $timeSlot->setServiceCloseAtAm($closeAtAm);
        $timeSlot->setServiceStartAtPm($startAtPm);
        $timeSlot->setServiceCloseAtPm($closeAtPm);
        $timeSlot->setIntervalTime(30);

        static::assertSame($expected, $this->timeSlotViewModelResolver->resolve($timeSlot));
    }

    

    public function provideTimeSlots(): iterable
    {
        $normalDay = [
            "10:00",
            "10:30",
            "11:00",
            "11:30",
            "12:00",
            "12:30",
            "13:00",
            "13:30",
            "14:00",
            " ",
            "18:00",
            "18:30",
            "19:00",
            "19:30",
            "20:00",
            "20:30",
            "21:00",
            "21:30",
            "22:00",
            "22:30",
            "23:00",
          ];

        $amDay = [
            "10:00",
            "10:30",
            "11:00",
            "11:30",
            "12:00",
            "12:30",
            "13:00",
            "13:30",
            "14:00",
        ];

        $pmDay = [
            "18:00",
            "18:30",
            "19:00",
            "19:30",
            "20:00",
            "20:30",
            "21:00",
            "21:30",
            "22:00",
            "22:30",
            "23:00",
        ];

        $continuousDay = [
            "10:00",
            "10:30",
            "11:00",
            "11:30",
            "12:00",
            "12:30",
            "13:00",
            "13:30",
            "14:00",
            "14:30",
            "15:00",
            "15:30",
            "16:00",
            "16:30",
            "17:00",
            "17:30",
            "18:00",
            "18:30",
            "19:00",
            "19:30",
            "20:00",
            "20:30",
            "21:00",
            "21:30",
            "22:00",
            "22:30",
            "23:00",
        ];

        // [$type, $startAtAm, $closeAtAm, $startAtPm, $closeAtPm, $timeSlot]
        yield "Normal day"      => [TimeSlot::NORMAL_DAY_TIMESLOT_STATUS,  TimeProvider::setTime(10, 00),  TimeProvider::setTime(14, 00),  TimeProvider::setTime(18, 00),  TimeProvider::setTime(23, 00), $normalDay];
        yield "closed  day"     => [TimeSlot::CLOSED_DAY_TIMESLOT_STATUS, null, null, null, null, ['Fermé']];
        yield "Am day"          => [TimeSlot::AM_TIMESLOT_STATUS, TimeProvider::setTime(10, 00), TimeProvider::setTime(14, 00), null, null, $amDay];
        yield "Pm Day"          => [TimeSlot::PM_TIMESLOT_STATUS, null, null, TimeProvider::setTime(18, 00), TimeProvider::setTime(23, 00), $pmDay];
        yield "continuous day"  => [TimeSlot::CONTINOUS_DAY_TIMESLOT_STATUS, TimeProvider::setTime(10, 00), TimeProvider::setTime(23, 00), null, null, $continuousDay];

    }
} 