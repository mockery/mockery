<?php

namespace PHP80;

use ArrayIterator;
use IteratorAggregate;

class ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator([]);
    }
}
