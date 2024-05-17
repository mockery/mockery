<?php

namespace PHP73;

abstract class TestWithProtectedMethods
{
    public function foo()
    {
        return $this->abstractProtected();
    }

    abstract protected function abstractProtected();

    public function bar()
    {
        return $this->protectedBar();
    }

    protected function protectedBar()
    {
        return 'bar';
    }
}
