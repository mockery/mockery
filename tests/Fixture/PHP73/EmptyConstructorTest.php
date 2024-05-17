<?php

namespace PHP73;

class EmptyConstructorTest
{
    public $numberOfConstructorArgs;

    public function __construct(...$args)
    {
        $this->numberOfConstructorArgs = count($args);
    }

    public function foo()
    {
    }
}
