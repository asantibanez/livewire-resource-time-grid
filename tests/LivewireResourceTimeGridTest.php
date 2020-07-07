<?php

namespace Asantibanez\LivewireResourceTimeGrid\Tests;

use Asantibanez\LivewireResourceTimeGrid\LivewireResourceTimeGrid;
use Livewire\LivewireManager;
use Livewire\Testing\TestableLivewire;

class LivewireResourceTimeGridTest extends TestCase
{
    private function createComponent($parameters) : TestableLivewire
    {
        return app(LivewireManager::class)->test(LivewireResourceTimeGrid::class, $parameters);
    }

    /** @test */
    public function can_create_component_with_required_parameters()
    {
        //Arrange
        $startingHour = 7;
        $endingHour = 8;
        $interval = 15;

        //Act
        $component = $this->createComponent([
            'startingHour' => $startingHour,
            'endingHour' => $endingHour,
            'interval' => $interval,
        ]);

        //Assert
        $this->assertNotNull($component);

        $component->assertSet('startingHour', $startingHour);
        $component->assertSet('endingHour', $endingHour);
        $component->assertSet('interval', $interval);

        $component->assertSet('resourceColumnHeaderHeightInRems', 4);
        $component->assertSet('hourHeightInRems', 8);

        $component->assertSet('gridView', 'livewire-resource-time-grid::grid');
        $component->assertSet('hoursColumnView', 'livewire-resource-time-grid::hours-column');
        $component->assertSet('hourView', 'livewire-resource-time-grid::hour');
        $component->assertSet('resourceColumnHeaderView', 'livewire-resource-time-grid::resource-column-header');
        $component->assertSet('eventView', 'livewire-resource-time-grid::event');

        $component->assertSet('beforeGridView', null);
        $component->assertSet('afterGridView', null);
    }
}
