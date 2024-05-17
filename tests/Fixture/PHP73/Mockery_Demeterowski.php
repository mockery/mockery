<?php

declare(strict_types=1);

namespace PHP73;

class Mockery_Demeterowski
{
    public function foo()
    {
        return $this;
    }

    public function bar()
    {
        return $this;
    }

    public function baz()
    {
        return 'Ham!';
    }
}
