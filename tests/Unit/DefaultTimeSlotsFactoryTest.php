<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Restaurant;
use App\Entity\TimeSlot;
use App\Services\TimeSlotServices\DefaultTimeSlotsFactory;
use PHPUnit\Framework\TestCase;

final class DefaultTimeSlotsFactoryTest extends TestCase
{
    private DefaultTimeSlotsFactory $timeSlotBuilder;
    
    protected function setUp(): void
    {
        $this->timeSlotBuilder = new DefaultTimeSlotsFactory();
    }

    public function testTimeSlotsCollectionSuccessFullCreated()
    {
        $restaurant =  new Restaurant();

        $this->timeSlotBuilder->createTimeSlots($restaurant);

        static::assertCount(7, $restaurant->getTimeSlots());
        static::assertContainsOnlyInstancesOf(TimeSlot::class, $restaurant->getTimeSlots());
    }
}