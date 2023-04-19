<?php

namespace MockeryTest\Fixture;

class MockeryTest_PartialStatic
{
    public static function mockMe($a)
    {
        return $a;
    }
    public static function keepMe($b)
    {
        return $b;
    }
}
