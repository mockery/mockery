<?php

declare(strict_types=1);

namespace PHP82;

class IterableObjectString
{
    public function __invoke(iterable|object|string $arg): void
    {
    }
}
