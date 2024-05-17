<?php

declare(strict_types=1);

namespace PHP82;

class TestOne implements ITest
{
// Acceptable. Everything that ITest accepts is still valid
// and then some.
    public function stuff((AInterface&BInterface)|DInterface|ZClass $arg): void
    {
    }
}
