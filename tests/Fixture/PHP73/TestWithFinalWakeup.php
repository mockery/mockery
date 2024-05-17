<?php

declare(strict_types=1);

namespace PHP73;

class TestWithFinalWakeup
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    final public function __wakeup()
    {
        return __METHOD__;
    }
}
