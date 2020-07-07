{{-- Injected variables: $resource, $hoursAndSlots, $interval --}}
<div
    class="-ml-px"
    style="min-width: 12rem;"
    id="{{ $resource['id'] }}"
>

    <div
        class="border"
        style="height: {{ $resourceColumnHeaderHeightInRems }}rem;"
    >
        @include($resourceColumnHeaderView, [
            'resource' => $resource,
        ])
    </div>

    @foreach($hoursAndSlots as $hourAndSlots)
        <div
            class="{{ $styles['hourAndSlotsContainer']}}"
            style="height: {{ $hourHeightInRems }}rem"
        >
            <div class="grid grid-cols-1 grid-rows-{{ 60/$interval }}">
                @foreach($hourAndSlots['slots'] as $slot)
                    @include($resourceColumnHourSlotView, [
                        'hour' => $hourAndSlots['hour'],
                        'slot' => $slot,
                        'interval' => $interval,
                        'events' => $resource['events'],
                        'eventsInHourSlot' => $getEventsInHourSlot($hourAndSlots['hour'], $slot, $resource['events']),
                    ])
                @endforeach
            </div>
        </div>
    @endforeach
</div>
