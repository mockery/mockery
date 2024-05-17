<?php

declare(strict_types=1);

namespace PHP80;

class ImplementsIteratorAggregate implements \IteratorAggregate
{
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator([]);
    }
}
