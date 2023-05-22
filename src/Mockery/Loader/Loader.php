<?php

declare(strict_types=1);

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

interface Loader
{
    public function load(MockDefinition $definition): void;
}
