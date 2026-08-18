<?php

namespace PHP74\MagicMethod;

class ClassWithCloneMethod implements InterfaceWithCloneMethod
{
    public function __clone(): void
    {
    }
}
