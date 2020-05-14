<div class="border-b"
     style="height: {{ $timeSlotHeight / (60/$interval) }}rem;"

     id="{{ $_instance->id }}-{{ $resource['id'] }}-{{ $timeSlot }}-{{$minute}}"
     ondragenter="onEventDragEnter(event, @this, '{{ $resource['id'] }}', {{ $timeSlot }}, {{ $minute }});"
     ondragleave="onEventDragLeave(event, @this, '{{ $resource['id'] }}', {{ $timeSlot }}, {{ $minute }});"
     ondragover="onEventDragOver(event);"
     ondrop="onEventDrop(event, @this, '{{ $resource['id'] }}', {{ $timeSlot }}, {{ $minute }});"
     wire:click.stop="timeSlotClick('{{ $resource['id'] }}', {{ $timeSlot }}, {{ $minute }})">

    @foreach($eventsInTimeSlot as $event)
        <div
            draggable="true"
            ondragstart="onEventDragStart(event, '{{ $event['id'] }}')"
            wire:click.stop=""
            class="absolute top-0 left-0"
            style="{{ $getEventStyles($event, $events) }}">
            @include($eventView, [
                'event' => $event,
            ])
        </div>
    @endforeach

</div>
