<?php

namespace MockeryTest\Fixture;

class Mockery_Magic
{
    public function __call($method, $args)
    {
        return 42;
    }
}
