<?php

namespace Asantibanez\LivewireResourceTimeGrid\Exceptions;

use DateTimeInterface;
use InvalidArgumentException;

class InvalidPeriod extends InvalidArgumentException
{
    public static function endBeforeStart(DateTimeInterface $start, DateTimeInterface $end): InvalidPeriod
    {
        return new static("The event end time `{$end->format('H:i')}` is before the start time `{$start->format('H:i')}`.");
    }
}
