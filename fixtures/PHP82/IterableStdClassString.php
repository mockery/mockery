<?php

namespace PHP82;

class IterableStdClassString
{
    public function __invoke(iterable|\stdClass|string $arg): void
    {
    }
}
