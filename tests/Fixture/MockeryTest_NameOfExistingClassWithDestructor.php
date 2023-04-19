<?php

namespace MockeryTest\Fixture;

class MockeryTest_NameOfExistingClassWithDestructor
{
    public static $isDestructorWasCalled = \false;
    public function __destruct()
    {
        self::$isDestructorWasCalled = \true;
    }
}
