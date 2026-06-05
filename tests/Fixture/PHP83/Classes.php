<?php

declare(strict_types=1);

namespace PHP83;

class Classes implements Interfaces {
    use Traits;
}

readonly class ReadonlyClass {
    public function foo(): string
    {
        return 'foo';
    }
}
