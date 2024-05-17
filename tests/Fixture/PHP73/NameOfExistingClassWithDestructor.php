<?php

namespace PHP73;

class NameOfExistingClassWithDestructor
{
    public static $isDestructorWasCalled = false;

    public function __destruct()
    {
        self::$isDestructorWasCalled = true;
    }
}
