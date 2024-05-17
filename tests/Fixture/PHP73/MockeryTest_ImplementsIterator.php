<?php

declare(strict_types=1);

namespace PHP73;


if (\PHP_VERSION_ID < 80000) {

    class MockeryTest_ImplementsIterator implements \Iterator
    {
        public function rewind()
        {
        }

        public function current()
        {
        }

        public function key()
        {
        }

        public function next()
        {
        }

        public function valid()
        {
        }
    }
}
