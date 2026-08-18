<?php

namespace PHP74\MagicMethod;

use ReturnTypeWillChange;

class ClassWithCloneMethod implements InterfaceWithCloneMethod
{
    #[ReturnTypeWillChange]
    public function __clone()
    {
    }
}
