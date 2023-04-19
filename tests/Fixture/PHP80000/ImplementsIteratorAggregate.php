<?php

namespace MockeryTest\Fixture\PHP80000;

use IteratorAggregate;

class ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator([]);
    }
}
