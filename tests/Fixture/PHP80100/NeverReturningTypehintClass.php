<?php

namespace MockeryTest\Fixture\PHP80100;

class NeverReturningTypehintClass
{
    public function throws(): never
    {
        throw new \RuntimeException('Never!');
    }
    public function exits(): never
    {
        exit(123);
    }
}
