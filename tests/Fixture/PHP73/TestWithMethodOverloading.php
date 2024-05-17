<?php

declare(strict_types=1);

namespace PHP73;

class TestWithMethodOverloading
{
    public function __call($name, $arguments)
    {
        return 42;
    }

    public function thisIsRealMethod()
    {
        return 1;
    }
}
