<?php

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;
use Mockery\Loader\Loader;

class RequireLoader implements Loader
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        $tmpfname = tempnam($this->path, "Mockery");
        file_put_contents($tmpfname, $definition->getCode());

        require $tmpfname;
    }
}
