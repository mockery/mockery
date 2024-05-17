<?php

declare(strict_types=1);

namespace PHP73;

abstract class TestWithVariadicArguments
{
    public function foo(...$bar)
    {
        return $bar;
    }
}
