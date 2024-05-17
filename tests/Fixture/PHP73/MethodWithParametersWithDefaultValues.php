<?php

declare(strict_types=1);

namespace PHP73;

class MethodWithParametersWithDefaultValues
{
    public function foo($bar = null)
    {
    }

    public function bar(?string $bar = null)
    {
    }
}
