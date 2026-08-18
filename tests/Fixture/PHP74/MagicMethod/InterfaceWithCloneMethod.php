<?php

namespace PHP74\MagicMethod;

use ReturnTypeWillChange;

interface InterfaceWithCloneMethod
{
    #[ReturnTypeWillChange]
    public function __clone();
}
