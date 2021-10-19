<div>
    <div>
        @includeIf($beforeGridView)
    </div>

    <div class="flex">

        @include($hoursColumnView, ['hoursAndSlots' => $hoursAndSlots])

        <div class="overflow-x-auto w-full">
            <div class="inline-block min-w-full overflow-hidden">
                <div class="grid grid-flow-col grid-cols-{{ $resources->count() }}"">
                    @foreach($resources as $resource)
                        @include($resourceColumnView, [
                            'hoursAndSlots' => $hoursAndSlots,
                            'resource' => $resource,
                            'interval' => $interval,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div>
        @includeIf($afterGridView)
    </div>
</div>



