{{-- Injected variables: $event --}}
<div
    class="{{ $styles['event'] }}"
    wire:click.stop="onEventClick('{{ $event['id'] }}')">

    <div class="text-xs font-medium bg-indigo-500 p-2 text-white">
        {{ $event['starts_at']->format('h:i A') }} - {{ $event['ends_at']->format('h:i A') }}
    </div>
    <div class="text-xs bg-white flex-1 p-2">
        <div>
            {{ $event['title'] }}
        </div>
    </div>
</div>
