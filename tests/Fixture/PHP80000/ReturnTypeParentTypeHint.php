<?php

namespace MockeryTest\Fixture\PHP80000;

class ReturnTypeParentTypeHint extends \stdClass
{
    public function foo(): parent
    {
    }
}
