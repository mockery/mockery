<?php

declare(strict_types=1);

namespace PHP82;

use stdClass;

class IterableStdClassString
{
    public function __invoke(iterable|stdClass|string $arg): void
    {
    }
}
