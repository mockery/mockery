<?php

namespace PHP73;

class ClassWithMagicCall
{
    public function foo()
    {
    }

    public function __call($method, $args)
    {
    }
}
