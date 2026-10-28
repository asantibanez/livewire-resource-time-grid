<?php

namespace Asantibanez\LivewireResourceTimeGrid\Tests;

use Asantibanez\LivewireResourceTimeGrid\LivewireResourceTimeGrid;
use Carbon\Carbon;
use Livewire\LivewireManager;
use Livewire\Testing\TestableLivewire;

class LivewireResourceTimeGridTest extends TestCase
{
    private function createComponent($parameters) : TestableLivewire
    {
        return app(LivewireManager::class)->test(LivewireResourceTimeGrid::class, $parameters);
    }

    private function invokePrivate($component, $methodName, array $arguments = [])
    {
        $method = new \ReflectionMethod(LivewireResourceTimeGrid::class, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($component, $arguments);
    }

    /** @test */
    public function non_divisor_interval_uses_a_partial_final_slot()
    {
        $component = new LivewireResourceTimeGrid();
        $component->mount(8, 8, 40);

        $hour = $this->invokePrivate($component, 'hoursAndSlots')->first();

        $this->assertSame([0, 40], $hour['slots']);
        $this->assertSame('40fr 20fr', $hour['slotGridRows']);
    }

    /** @test */
    public function interval_of_fifty_nine_minutes_keeps_the_final_one_minute_slot()
    {
        $component = new LivewireResourceTimeGrid();
        $component->mount(8, 8, 59);

        $hour = $this->invokePrivate($component, 'hoursAndSlots')->first();

        $this->assertSame([0, 59], $hour['slots']);
        $this->assertSame('59fr 1fr', $hour['slotGridRows']);
    }

    /** @test */
    public function non_divisor_interval_does_not_duplicate_events_across_hours()
    {
        $component = new LivewireResourceTimeGrid();
        $component->mount(8, 9, 40);
        $events = collect([
            ['id' => 1, 'starts_at' => Carbon::parse('2026-10-28 08:50:00', 'UTC')],
            ['id' => 2, 'starts_at' => Carbon::parse('2026-10-28 09:10:00', 'UTC')],
        ]);

        $lastSlotOfEight = $this->invokePrivate($component, 'getEventsInHourSlot', [8, 40, $events]);
        $firstSlotOfNine = $this->invokePrivate($component, 'getEventsInHourSlot', [9, 0, $events]);

        $this->assertSame([1], array_values($lastSlotOfEight->pluck('id')->all()));
        $this->assertSame([2], array_values($firstSlotOfNine->pluck('id')->all()));
    }

    /** @test */
    public function interval_must_be_a_positive_whole_number_below_sixty()
    {
        $this->expectException(\InvalidArgumentException::class);

        $component = new LivewireResourceTimeGrid();
        $component->mount(8, 8, 0);
    }

    /** @test */
    public function interval_must_be_below_sixty_minutes()
    {
        $this->expectException(\InvalidArgumentException::class);

        $component = new LivewireResourceTimeGrid();
        $component->mount(8, 8, 60);
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
