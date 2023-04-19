<?php

namespace MockeryTest\Fixture;

class MockeryTest_MethodParamRef
{
    public function method1(&$foo)
    {
        return \true;
    }
}
