<div>
    <div>
        @if($beforeGridView)
            @include($beforeGridView)
        @endif
    </div>

    <div class="flex">
        @include('livewire-resource-time-grid::time-column', [
                   'timeSlots' => $timeSlots,
                   'interval' => $interval,
        ])
        <div class="overflow-x-auto w-full">
            <div class="inline-block min-w-full overflow-hidden">
                <div class="grid grid-flow-col">
                    @foreach($resources as $resource)
                        @include('livewire-resource-time-grid::resource-column', [
                            'timeSlots' => $timeSlots,
                            'resource' => $resource,
                            'interval' => $interval,
                            'events' => $getEventsForResource($resource, $events),
                            'resourceHeaderView' => $resourceHeaderView,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div>
        @if($afterGridView)
            @include($afterGridView)
        @endif
    </div>
</div>



