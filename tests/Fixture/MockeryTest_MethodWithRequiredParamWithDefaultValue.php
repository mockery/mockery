<?php

namespace MockeryTest\Fixture;

class MockeryTest_MethodWithRequiredParamWithDefaultValue
{
    public function foo(\DateTime $bar = \null, $baz)
    {
    }
}
