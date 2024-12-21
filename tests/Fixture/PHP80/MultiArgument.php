<?php

declare(strict_types=1);

namespace PHP80;

class MultiArgument
{
    public function foo(int $bar = 0, string $bee = '', string $dol = '')
    {
    }

    public function bar(int $int, string $string, bool $bool)
    {
    }
}
