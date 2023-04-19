<?php

namespace MockeryTest\Fixture;

class MockeryTest_ImplementsIteratorAggregate implements \IteratorAggregate
{
    public function getIterator()
    {
        return new \ArrayIterator(array());
    }
}
