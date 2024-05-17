<?php

namespace PHP73;

class MockeryTest_MockCallableTypeHint
{
    public function foo(callable $baz)
    {
        $baz();
    }

    public function bar(?callable $callback = null)
    {
        $callback();
    }
}
