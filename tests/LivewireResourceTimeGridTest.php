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
        $component->assertSet('interval', $interval);

        $component->assertSet('resourceHeight', 3);
        $component->assertSet('timeSlotHeight', 7);

        $component->assertSet('resourceHeaderView', 'livewire-resource-time-grid::resource-header');
        $component->assertSet('eventView', 'livewire-resource-time-grid::event');

        $component->assertSet('beforeGridView', null);
        $component->assertSet('afterGridView', null);
    }
}
