<?php

declare(strict_types=1);

namespace PHP83;

interface Interfaces {
    const string BAR = Enums::FOO;

    public function foo(): string;
}
