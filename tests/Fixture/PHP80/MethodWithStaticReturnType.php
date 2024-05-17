<?php

declare(strict_types=1);

namespace PHP80;

class MethodWithStaticReturnType
{
    public function returnType(): static
    {
        return $this;
    }
}
