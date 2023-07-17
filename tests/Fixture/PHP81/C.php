<?php

declare(strict_types=1);

namespace Fixture\PHP81;

class C
{
    public function __construct(
        private int $x = 1,
        private B $b = new B(1)
    ) {
    }

    public function test(): int
    {
        return $this->x + $this->b->test();
    }
}
