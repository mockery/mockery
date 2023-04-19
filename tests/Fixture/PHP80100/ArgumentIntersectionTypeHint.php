<?php

namespace MockeryTest\Fixture\PHP80100;

class ArgumentIntersectionTypeHint
{
    public function foo(IntersectionTypeHelper1Interface&IntersectionTypeHelper2Interface $foo)
    {
    }
}
