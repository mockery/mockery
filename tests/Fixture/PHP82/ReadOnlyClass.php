<?php

declare(strict_types=1);

namespace PHP82;

readonly class ReadonlyClass {
    public function foo(): string
    {
        return 'foo';
    }
}
