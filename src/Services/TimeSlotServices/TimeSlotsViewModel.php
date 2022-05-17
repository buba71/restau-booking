<?php 

declare(strict_types=1);

namespace App\Services\TimeSlotServices;
final class TimeSlotsViewModel
{
    public function __construct(private TimeSlotViewModelResolver $timeSlotViewModel)
    {
        date_default_timezone_set("Europe/Paris");
    }

    /**
     * @param int $restaurantId
     * @param string $today
     * 
     * @return array<string[]>
     * 
     * TODO refactor method naming
     */
    public function transformModel(array $timeSlots): array
    {
        $timeSlotsViewModel = [];
        foreach($timeSlots as $timeSlot) {

            $timeSlotsViewModel[] = $this->timeSlotViewModel->resolve($timeSlot);
        }

        return $timeSlotsViewModel;
    }
}
