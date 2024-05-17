<?php

declare(strict_types=1);

namespace DemeterChain;

class A
{
    public function foo(): B
    {
        return new B();
    }
}
