<?php

namespace PHP73;

class MockeryTest_MethodParamRef2
{
    public function method1(&$foo)
    {
        return true;
    }
}
