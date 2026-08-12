<?php

declare(strict_types=1);

namespace PHP83;

readonly class ReadonlyClass {
    public function foo(): string
    {
        return 'foo';
    }
}
