<?php

namespace Asantibanez\LivewireResourceTimeGrid;

use Asantibanez\LivewireResourceTimeGrid\Exceptions\InvalidPeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Class LivewireResourceTimeGrid
 * @package Asantibanez\LivewireResourceTimeGrid
 * @property string $gridView
 * @property string $hoursColumnView
 * @property string $hourView
 * @property string $resourceColumnView
 * @property string $resourceColumnHeaderView
 * @property string $resourceColumnHourSlotView
 * @property string $eventView
 * @property int $hourHeightInRems
 * @property int $resourceColumnHeaderHeightInRems
 */
class LivewireResourceTimeGrid extends Component
{
    public $startingHour;
    public $endingHour;
    public $interval;

    public $gridView;
    public $hoursColumnView;
    public $hourView;
    public $resourceColumnView;
    public $resourceColumnHeaderView;
    public $resourceColumnHourSlotView;
    public $eventView;

    public $hourHeightInRems;
    public $resourceColumnHeaderHeightInRems;

    public $beforeGridView;
    public $afterGridView;

    public function mount($startingHour,
                          $endingHour,
                          $interval,
                          $gridView = null,
                          $hoursColumnView = null,
                          $hourView = null,
                          $resourceColumnView = null,
                          $resourceColumnHeaderView = null,
                          $resourceColumnHourSlotView = null,
                          $eventView = null,
                          $beforeGridView = null,
                          $afterGridView = null,
                          $resourceColumnHeaderHeightInRems = 4,
                          $hourHeightInRems = 8,
                          $extras = null)
    {
        $this->startingHour = $startingHour;
        $this->endingHour = $endingHour;
        $this->interval = $interval;

        $this->gridView = $gridView ?? 'livewire-resource-time-grid::grid';
        $this->hoursColumnView = $hoursColumnView ?? 'livewire-resource-time-grid::hours-column';
        $this->hourView = $hourView ?? 'livewire-resource-time-grid::hour';
        $this->resourceColumnView = $resourceColumnView ?? 'livewire-resource-time-grid::resource-column';
        $this->resourceColumnHeaderView = $resourceColumnHeaderView ?? 'livewire-resource-time-grid::resource-column-header';
        $this->resourceColumnHourSlotView = $resourceColumnHourSlotView ?? 'livewire-resource-time-grid::resource-column-hour-slot';
        $this->eventView = $eventView ?? 'livewire-resource-time-grid::event';

        $this->beforeGridView = $beforeGridView;
        $this->afterGridView = $afterGridView;

        $this->hourHeightInRems = $hourHeightInRems;
        $this->resourceColumnHeaderHeightInRems = $resourceColumnHeaderHeightInRems;

        $this->afterMount($extras);
    }

    public function afterMount($extras)
    {
        //
    }

    public function resources()
    {
        return collect();
    }

    public function events()
    {
        return collect();
    }

    public function isEventForResource($event, $resource)
    {
        return $event['resource_id'] == $resource['id'];
    }

    public function hourSlotClick($resourceId, $hour, $slot)
    {
        //
    }

    public function onEventClick($eventId)
    {
        //
    }

    public function onEventDropped($eventId, $resourceId, $hour, $slot)
    {
        //
    }

    public function styles()
    {
        return [
            'intersect' => 'border',

            'hourAndSlotsContainer' => 'border relative -mt-px bg-gray-100',

            'hourWrapper' => 'border relative -mt-px bg-white',

            'hour' => 'p-2 text-xs text-gray-600 flex justify-center items-center',

            'resourceColumnHeader' => 'h-full text-xs flex justify-center items-center',

            'resourceColumnHourSlot' => 'border-b hover:bg-blue-100 cursor-pointer',

            'eventWrapper' => 'absolute top-0 left-0',

            'event' => 'rounded h-full flex flex-col overflow-hidden w-full shadow-lg border',

            'eventTitle' => 'text-xs font-medium bg-indigo-500 p-2 text-white',

            'eventBody' => 'text-xs bg-white flex-1 p-2',
        ];
    }

    public function render()
    {
        $events = $this->getCheckedEvents();

        $resources = $this->resources()
            ->map(function ($resource) use ($events) {
                $resource['events'] = $this->getEventsForResource($resource, $events);
                return $resource;
            });

        return view($this->gridView)
            ->with('hoursAndSlots', $this->hoursAndSlots())
            ->with('resources', $resources)
            ->with('styles', $this->styles())
            ->with('getEventsInHourSlot', function ($hour, $slot, $events) {
                return $this->getEventsInHourSlot($hour, $slot, $events);
            })
            ->with('getEventStyles', function ($event, $events) {
                return $this->getEventStyles($event, $events);
            })
            ;
    }

    private function isMidnight(Carbon $time): bool
    {
        return $time->format('H:i') === '00:00';
    }

    private function getCheckedEvents(): Collection
    {
        return $this->events()
            ->map(function($event) {
                if(!$event['starts_at']->isSameDay($event['ends_at'])) {
                    $event['ends_at'] = (clone $event['starts_at'])
                                            ->startOfDay()
                                            ->setHour($event['ends_at']->format('G'))
                                            ->setMinute($event['ends_at']->format('i'));
                }
                if($this->isMidnight($event['ends_at'])) {
                    $event['ends_at']->addDays(1);
                }
                return $event;
            })
            ->each(function($event) {
                if(
                    !$this->isMidnight($event['ends_at'])
                    && $event['ends_at']->isBefore($event['starts_at'])
                ) {
                    throw InvalidPeriod::endBeforeStart($event['starts_at'], $event['ends_at']);
                }
            });
    }

    private function hoursAndSlots()
    {
        return collect(range($this->startingHour, $this->endingHour))
            ->map(function ($hour) {
                return [
                    'hour' => $hour,
                    'slots' => range(0, 60 - $this->interval, $this->interval)
                ];
            });
    }

    private function getEventConflictingEvents($event, $events, $conflictingEvents) : Collection
    {
        $eventConflictingNeighborEvents = $this->getEventConflictingNeighborEvents($event, $events);

        $notInConflictingEvents = $eventConflictingNeighborEvents
            ->reject(function ($event) use ($conflictingEvents) {
                return $conflictingEvents->contains($event);
            });

        $conflictingEvents = $conflictingEvents->merge($notInConflictingEvents);

        return $conflictingEvents
            ->merge(
                $notInConflictingEvents->flatMap(function ($event) use ($events, $conflictingEvents) {
                    return $this->getEventConflictingEvents($event, $events, $conflictingEvents);
                })
            )
            ->unique('id')
            ->values();
    }

    private function getEventConflictingNeighborEvents($event, $events) : Collection
    {
        if($this->isMidnight($event['ends_at'])) {
            $event['ends_at'] = (clone $event['ends_at'])->subMinutes(1);
        }
        return $events
            ->filter(function ($item) use ($event) {
                if($this->isMidnight($item['ends_at'])) {
                    $item['ends_at'] = (clone $item['ends_at'])->subMinutes(1);
                }

                return (
                        $event['starts_at']->betweenIncluded($item['starts_at'], $item['ends_at'])
                        && $event['ends_at']->betweenIncluded($item['starts_at'], $item['ends_at'])
                    ) || (
                    $event['starts_at']->betweenExcluded($item['starts_at'], $item['ends_at'])
                    ) || (
                    $event['ends_at']->betweenExcluded($item['starts_at'], $item['ends_at'])
                    ) || (
                    $item['starts_at']->betweenExcluded($event['starts_at'], $event['ends_at'])
                    ) || (
                    $item['ends_at']->betweenExcluded($event['starts_at'], $event['ends_at'])
                    );
            })
            ->values();
    }

    private function getEventsForResource($resource, Collection $events) : Collection
    {
        return $events
            ->filter(function ($event) use ($resource) {
                return $this->isEventForResource($event, $resource);
            });
    }

    private function getEventsInHourSlot($hour, $slot, Collection $events) : Collection
    {
        return $events
            ->filter(function ($event) use ($hour, $slot) {
                /** @var Carbon $eventStartsAt */
                $eventStartsAt = $event['starts_at'];

                /** @var Carbon $hourSlotStartsAt */
                $hourSlotStartsAt = $eventStartsAt->clone()
                    ->setTime($hour, $slot);

                /** @var Carbon $hourSlotEndsAt */
                $hourSlotEndsAt = $eventStartsAt->clone()
                    ->setTime($hour, $slot)
                    ->addMinutes($this->interval);

                return $eventStartsAt->timestamp >= $hourSlotStartsAt->timestamp
                    && $eventStartsAt->timestamp < $hourSlotEndsAt->timestamp
                    ;
            });
    }

    private function eventHourSlotFraction($event)
    {
        return $event['starts_at']->minute / $this->interval;
    }

    public function hourSlotIntervalHeightInRems()
    {
        return $this->hourHeightInRems / (60/$this->interval);
    }

    private function getEventStyles($event, $events)
    {
        $conflictingEvents = $this->getEventConflictingEvents($event, $events, collect());

        $eventIndex = $conflictingEvents
            ->sortBy('id')
            ->values()
            ->search($event);

        $marginTop = $this->eventHourSlotFraction($event) * $this->hourSlotIntervalHeightInRems();

        $height = $event['starts_at']->diffInMinutes($event['ends_at']) / $this->interval * $this->hourSlotIntervalHeightInRems();

        $height -= 0.5; // Magic fix 😅

        $width = $conflictingEvents->count() > 0
            ? 95 / $conflictingEvents->count()
            : 95
        ;

        $marginLeft = $eventIndex == 0
            ? 0
            : $eventIndex * $width + $eventIndex
        ;

        $zIndex = ($eventIndex + 1) * 100;

        return collect([
            "margin-left: {$marginLeft}%",
            "margin-top: {$marginTop}rem",
            "height: {$height}rem",
            "width: {$width}%",
            "z-index: {$zIndex};",
        ])->implode('; ');
    }
}
