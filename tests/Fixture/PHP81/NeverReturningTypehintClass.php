<?php

declare(strict_types=1);

namespace PHP81;

use RuntimeException;

class NeverReturningTypehintClass
{
    public function throws(): never
    {
        throw new RuntimeException('Never!');
    }

    public function exits(): never
    {
        exit(123);
    }
}
