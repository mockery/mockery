<?php

namespace MockeryTest\Fixture\PHP80000;

class ArgumentUnionTypeHint
{
    public function foo(string|array|self $foo)
    {
    }
}
