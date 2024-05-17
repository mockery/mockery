<?php

declare(strict_types=1);

namespace PHP73;

class MethodWithNullableTypedParameter
{
    public function foo(?string $bar)
    {
    }

    public function bar(?string $bar = null)
    {
    }

    public function baz(?string $bar = null)
    {
    }
}
