<?php

declare(strict_types=1);

namespace Fixture\PHP81;

class A
{
    public function __construct(
        private int $x = 1
    ) {
    }

    public function test(): int
    {
        return $this->x;
    }
}
