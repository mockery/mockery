<?php

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

interface Loader
{
    public function load(MockDefinition $definition);
}
