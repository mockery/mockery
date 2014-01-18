<?php

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

interface Loader
{
    function load(MockDefinition $definition);
}
