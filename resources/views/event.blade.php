<div
    class="rounded h-full flex flex-col overflow-hidden w-full shadow"
    wire:click.stop="onEventClick('{{ $event['id'] }}')">

    <div class="text-xs font-medium bg-indigo-500 p-2 text-white">
        {{ $event['starts_at']->format('h:i A') }} - {{ $event['ends_at']->format('h:i A') }}
    </div>
    <div class="text-xs bg-indigo-100 flex-1 p-2">
        <div>
            {{ $event['title'] }}
        </div>
    </div>
</div>
