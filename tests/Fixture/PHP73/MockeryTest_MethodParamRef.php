<?php

namespace PHP73;

class MockeryTest_MethodParamRef
{
    public function method1(&$foo)
    {
        return true;
    }
}
