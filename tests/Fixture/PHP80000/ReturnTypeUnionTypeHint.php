<?php

namespace MockeryTest\Fixture\PHP80000;

class ReturnTypeUnionTypeHint
{
    public function foo(): ReturnTypeMixedTypeHint|self
    {
    }
}
