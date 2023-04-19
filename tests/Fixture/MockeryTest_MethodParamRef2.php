<?php

namespace MockeryTest\Fixture;

class MockeryTest_MethodParamRef2
{
    public function method1(&$foo)
    {
        return \true;
    }
}
