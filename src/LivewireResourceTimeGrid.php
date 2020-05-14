<?php

namespace Asantibanez\LivewireResourceTimeGrid;

use Illuminate\Support\Collection;
use Livewire\Component;

class LivewireResourceTimeGrid extends Component
{
    public $resourceHeight;
    public $timeSlotHeight;

    public $startingHour;
    public $endingHour;
    public $interval;

    public $resourceHeaderView;
    public $eventView;

    public $beforeGridView;
    public $afterGridView;

    public function mount($startingHour,
                          $endingHour,
                          $interval,
                          $resourceHeaderView = null,
                          $eventView = null,
                          $beforeGridView = null,
                          $afterGridView = null,
                          $resourceHeight = 3,
                          $timeSlotHeight = 7)
    {
        $this->startingHour = $startingHour;
        $this->endingHour = $endingHour;
        $this->interval = $interval;

        $this->resourceHeaderView = $resourceHeaderView ?? 'livewire-resource-time-grid::resource-header';
        $this->eventView = $eventView ?? 'livewire-resource-time-grid::event';

        $this->beforeGridView = $beforeGridView;
        $this->afterGridView = $afterGridView;

        $this->resourceHeight = $resourceHeight;
        $this->timeSlotHeight = $timeSlotHeight;
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
        return true;
    }

    public function timeSlotClick($resourceId, $hour, $minute)
    {
        //
    }

    public function onEventClick($event)
    {
        //
    }

    public function onEventDropped($eventId, $resourceId, $timeSlot, $minute)
    {
        //
    }

    public function render()
    {
        $events = $this->events();

        return view('livewire-resource-time-grid::grid')
            ->with('timeSlots', $this->timeSlots())
            ->with('resources', $this->resources())
            ->with('events', $events)
            ->with('getEventsForResource', function ($resource, $events) {
                return $this->getEventsForResource($resource, $events);
            })
            ->with('getEventsInTimeSlot', function ($timeSlot, $minute, $events) {
                return $this->getEventsInTimeSlot($timeSlot, $minute, $events);
            })
            ->with('getEventStyles', function ($event, $events) {
                return $this->getEventStyles($event, $events);
            });
    }

    private function timeSlots()
    {
        return range($this->startingHour, $this->endingHour);
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
        return $events
            ->filter(function ($item) use ($event) {
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

    private function getEventsInTimeSlot($timeSlot, $minute, Collection $events) : Collection
    {
        return $events
            ->filter(function ($event) use ($timeSlot, $minute) {
                $timeSlotStartsAt = $event['starts_at']->clone()->setTime($timeSlot, $minute);

                return $event['starts_at']->isSameMinute($timeSlotStartsAt);
            });
    }

    private function getEventTimeSlotFraction($event)
    {
        return $event['starts_at']->minute / $this->interval;
    }

    public function timeSlotIntervalHeight()
    {
        return $this->timeSlotHeight / (60/$this->interval);
    }

    private function getEventStyles($event, $events)
    {
        $conflictingEvents = $this->getEventConflictingEvents($event, $events, collect());

        $eventIndex = $conflictingEvents
            ->sortBy('id')
            ->values()
            ->search($event);

        $marginTop = $this->getEventTimeSlotFraction($event) * $this->timeSlotIntervalHeight();

        $height = $event['starts_at']->diffInMinutes($event['ends_at']) / $this->interval * $this->timeSlotIntervalHeight();

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
