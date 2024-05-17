<?php

namespace PHP73;

class TestWithFinalToString
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    final public function __toString()
    {
        return __METHOD__;
    }
}
