<?php

namespace PHP80;

class ArgumentUnionTypeHint
{
    public function foo(string|array|self $foo)
    {
    }
}
