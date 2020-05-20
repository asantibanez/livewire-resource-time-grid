<?php

namespace Asantibanez\LivewireResourceTimeGrid;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LivewireResourceTimeGridServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-resource-time-grid');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/livewire-resource-time-grid'),
            ], 'livewire-resource-time-grid');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        Blade::directive('livewireResourceTimeGridScripts', function () {
            return <<<'HTML'
            <script>
                function onEventDragStart(event, eventId) {
                    event.dataTransfer.setData('id', eventId);
                }

                function onEventDragEnter(event, component, resourceId, timeSlot, minute) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${timeSlot}-${minute}`);
                    element.className = element.className + ' bg-indigo-100 ';
                }

                function onEventDragLeave(event, component, resourceId, timeSlot, minute) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${timeSlot}-${minute}`);
                    element.className = element.className.replace('bg-indigo-100', '');
                }

                function onEventDragOver(event) {
                    event.stopPropagation();
                    event.preventDefault();
                }

                function onEventDrop(event, component, resourceId, timeSlot, minute) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${timeSlot}-${minute}`);
                    element.className = element.className.replace('bg-indigo-100', '');

                    const eventId = event.dataTransfer.getData('id');
                    component.call('onEventDropped', eventId, resourceId, timeSlot, minute);
                }
            </script>
HTML;
        });
    }
}
