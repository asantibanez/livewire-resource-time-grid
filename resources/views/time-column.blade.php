<div style="min-width: 6rem;">
    {{-- Empty for resources row --}}
    <div class="border" style="height: {{ $resourceHeight }}rem"></div>

    @foreach($timeSlots as $timeSlot)
        <div class="border relative -mt-px bg-white" style="height: {{ $timeSlotHeight }}rem;">
            <div class="p-2 text-xs text-gray-600 flex justify-center items-center">
                {{ today()->setHour($timeSlot)->format('h:i A') }}
            </div>
        </div>
    @endforeach
</div>
