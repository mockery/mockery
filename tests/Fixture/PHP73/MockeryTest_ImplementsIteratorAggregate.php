<?php

declare(strict_types=1);

namespace PHP73;

if (\PHP_VERSION_ID < 80000) {
    class MockeryTest_ImplementsIteratorAggregate implements \IteratorAggregate
    {
        public function getIterator()
        {
            return new \ArrayIterator([]);
        }
    }
}
