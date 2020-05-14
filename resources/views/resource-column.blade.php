<div class="-ml-px" style="min-width: 12rem;" id="{{ $resource['id'] }}">

    <div class="border" style="height: {{ $resourceHeight }}rem">
        @include($resourceHeaderView, [
            'resource' => $resource,
            'events' => $events,
        ])
    </div>

    @foreach($timeSlots as $timeSlot)
        <div class="border relative -mt-px bg-gray-100" style="height: {{ $timeSlotHeight }}rem;">
            <div class="grid grid-cols-1 grid-rows-{{ 60/$interval }}">
                @foreach(range(0, 60 - $interval, $interval) as $minute)
                    @include('livewire-resource-time-grid::resource-column-time-slot', [
                        'timeSlot' => $timeSlot,
                        'minute' => $minute,
                        'events' => $events,
                        'eventsInTimeSlot' => $getEventsInTimeSlot($timeSlot, $minute, $events),
                        'eventView' => $eventView,
                    ])
                @endforeach
            </div>
        </div>
    @endforeach
</div>
