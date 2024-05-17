<?php

declare(strict_types=1);

namespace PHP80;

class ArgumentUnionTypeHintWithNull
{
    public function foo(string|array|null $foo)
    {
    }
}
