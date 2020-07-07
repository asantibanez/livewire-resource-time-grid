{{-- Injected variables: $hoursAndSlots --}}
<div style="min-width: 6rem;">

    <div
        class="{{ $styles['intersect'] }}"
        style="height: {{ $resourceColumnHeaderHeightInRems }}rem;"
    >
        {{-- Empty slot --}}
    </div>

    @foreach($hoursAndSlots as $hourAndSlots)
        @include($hourView, [
            'hourAndSlots' => $hourAndSlots,
        ])
    @endforeach

</div>
