<?php

namespace Mockery\Loader;

use Mockery\Loader\Loader;
use Mockery\Generator\MockDefinition;

class EvalLoader implements Loader
{
    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        eval("?>" . $definition->getCode());
    }
}
