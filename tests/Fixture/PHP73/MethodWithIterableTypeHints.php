<?php
declare(strict_types=1);

namespace PHP73;

class MethodWithIterableTypeHints
{
    public function foo(iterable $bar): iterable
    {
    }
}
