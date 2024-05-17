<?php

namespace PHP73;

final class UnmockableClass
{
    public function anyMethod()
    {
        return 1;
    }

    public function __call($method, $args)
    {
        return 42;
    }
}
