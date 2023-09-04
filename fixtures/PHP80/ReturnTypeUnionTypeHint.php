<?php

namespace PHP80;

class ReturnTypeUnionTypeHint
{
    public function foo(): ReturnTypeMixedTypeHint|self
    {
    }
}
