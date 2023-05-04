<?php

declare(strict_types=1);

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

final class EvalLoader implements Loader
{
    public function load(MockDefinition $definition): void
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        eval('?>' . $definition->getCode());
    }
}
