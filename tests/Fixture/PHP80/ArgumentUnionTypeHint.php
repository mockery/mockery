<?php

declare(strict_types=1);

namespace PHP80;

class ArgumentUnionTypeHint
{
    public function foo(string|array|self $foo)
    {
    }
}
