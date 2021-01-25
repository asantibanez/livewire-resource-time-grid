<?php

namespace Asantibanez\LivewireResourceTimeGrid;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LivewireResourceTimeGridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-resource-time-grid');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/livewire-resource-time-grid'),
            ], 'livewire-resource-time-grid');
        }

        Blade::directive('livewireResourceTimeGridScripts', function () {
            return <<<'HTML'
            <script>
                function onLivewireResourceTimeGridEventDragStart(event, eventId) {
                    event.dataTransfer.setData('id', eventId);
                }

                function onLivewireResourceTimeGridEventDragEnter(event, component, resourceId, hour, slot) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${hour}-${slot}`);
                    element.className = element.className + ' bg-indigo-100 ';
                }

                function onLivewireResourceTimeGridEventDragLeave(event, component, resourceId, hour, slot) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${hour}-${slot}`);
                    element.className = element.className.replace('bg-indigo-100', '');
                }

                function onLivewireResourceTimeGridEventDragOver(event) {
                    event.stopPropagation();
                    event.preventDefault();
                }

                function onLivewireResourceTimeGridEventDrop(event, component, resourceId, hour, slot) {
                    event.stopPropagation();
                    event.preventDefault();

                    let element = document.getElementById(`${component.id}-${resourceId}-${hour}-${slot}`);
                    element.className = element.className.replace('bg-indigo-100', '');

                    const eventId = event.dataTransfer.getData('id');
                    component.call('onEventDropped', eventId, resourceId, hour, slot);
                }
            </script>
HTML;
        });
    }

    public function register()
    {
        //
    }
}
