<?php

declare(strict_types=1);

namespace PHP82;

class IterableObject
{
    public function __invoke(iterable|object $arg): void
    {
    }
}
