<?php

namespace MockeryTest\Fixture;

class ClassWithToString
{
    public function __toString()
    {
        return 'foo';
    }
}
