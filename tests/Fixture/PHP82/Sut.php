<?php

declare(strict_types=1);

namespace PHP82;

class Sut
{
    public function foo(AInterface|(BInterface&CInterface) $arg)
    {
    }
}
