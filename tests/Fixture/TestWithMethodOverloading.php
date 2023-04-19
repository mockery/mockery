<?php

namespace MockeryTest\Fixture;

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
