<?php

declare(strict_types=1);

namespace PHP80;

class ReturnTypeUnionTypeHint
{
    public function foo(): ReturnTypeMixedTypeHint|self
    {
    }
}
