{{-- Injected variables: $hourAndSlots --}}
<div
    class="{{ $styles['hourWrapper'] }}"
    style="height: {{ $hourHeightInRems }}rem"
>
    <div class="{{ $styles['hour'] }}">
        {{ today()->setHour($hourAndSlots['hour'])->format('h:i A') }}
    </div>
</div>
