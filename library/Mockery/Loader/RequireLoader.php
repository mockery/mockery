<?php

namespace Mockery\Loader;

use Mockery\Loader\Loader;
use Mockery\Generator\MockDefinition;

class RequireLoader implements Loader
{
    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName())) {
            return;
        }

        $tmpfname = tempnam(sys_get_temp_dir(), "Mockery") . ".php";
        file_put_contents($tmpfname, $definition->getCode());

        require $tmpfname;
    } 
}
