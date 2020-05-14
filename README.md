# Livewire Resource Time Grid

This package allows you to build resource/time grid to show events in a "calendar" way. You can define resources as 
anything that owns an event, eg. a particular day, a user, a client, etc. Events loaded with the component will be then
rendered in columns according to the resource it belongs to and the starting date of the event. 

## Preview

![preview](https://github.com/asantibanez/livewire-dependant-select-demo/raw/master/preview.gif)

## Installation

You can install the package via composer:

```bash
composer require asantibanez/livewire-resource-time-grid
```

## Requirements

This package uses `livewire/livewire` (https://laravel-livewire.com/) under the hood.

It also uses TailwindCSS (https://tailwindcss.com/) for base styling. 

Please make sure you include both of this dependencies before using this component. 

## Usage

In order to use this component, you must create a new Livewire component that extends from 
`LivewireResourceTimeGrid`

You can use `make:livewire` to create a new component. For example.
``` bash
php artisan make:livewire AppointmentsGrid
```

In the `AppointmentsGrid` class, instead of extending from the base `Component` Livewire class, 
extend from `LivewireResourceTimeGrid`. Also, remove the `render` method. 
You'll have a class similar to this snippet.
 
``` php
class AppointmentsGrid extends LivewireResourceTimeGrid
{
    //
}
```

In this class, you must override the following methods

```php
public function resources()
{
    // must return a Laravel collection
}

public function events()
{
    // must return a Laravel collection
}
```

In `resources()` method, return a collection holding the "resources" that own the events
that are going to be listed in the grid. These "resources" must be arrays with `key => value` pairs
and must include an `id` and a `title`. You can add any other keys to each "resource as needed"

Example

```php
public function resources()
{
    return collect([
        ['id' => 'andres', 'title' => 'Andres'],
        ['id' => 'pamela', 'title' => 'Pamela'],
        ['id' => 'sara', 'title' => 'Sara'],
        ['id' => 'bruno', 'title' => 'Bruno'],
    ]);
}
```

In the `events()` method, return a collection holding the events that belong to each of the "resources"
returned in the `resources()` method. Events must also be keyed arrays holding at least the following keys: 
`id`, `title`, `starts_at`, `ends_at`, `resource_id`. 

Also, the following conditions are expected for each returned event: 
- For each event `resource_id` must match an `id` in the `resources()` returned collection.
- `starts_at` must be a `Carbon\Carbon` instance
- `ends_at` must be a `Carbon\Carbon` instance

Example

```php
public function events()
{
    return collect([
        [
            'id' => 1,
            'title' => 'Breakfast',
            'starts_at' => Carbon::today()->setTime(10, 0),
            'ends_at' => Carbon::today()->setTime(12, 0),
            'resource_id' => 'andres',
        ],
        [
            'id' => 2,
            'title' => 'Lunch',
            'starts_at' => Carbon::today()->setTime(13, 0),
            'ends_at' => Carbon::today()->setTime(15, 0),
            'resource_id' => 'pamela',
        ],
    ]);
}
```

Now, we can include our component in any view. You must specify 3 parameters, 
`starting-hour`, `ending-hour` and `interval`. These parameters represent the times of a day the grid will render
and how many divisions per hour it will display. (`interval` must be in minutes and less than `60`)

Example

```blade
<livewire:appointments-grid
    starting-hour="8"
    ending-hour="19"
    interval="15"
/>
``` 

You should include scripts with `@livewireResourceTimeGrid` to enable drag and drop which is turned on by default.
You must include them after `@livewireScripts`

```blade
@livewireScripts
@livewireResourceTimeGridScripts
``` 

This will render a grid starting from 8am til 7pm inclusive with time slots of 15 minutes.

![preview](https://github.com/asantibanez/livewire-dependant-select-demo/raw/master/example.png)

By default, the component uses all the available width and height. 
You can constrain it to use a specific set of dimensions with a wrapper element.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email santibanez.andres@gmail.com instead of using the issue tracker.

## Credits

- [Andrés Santibáñez](https://github.com/asantibanez)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
