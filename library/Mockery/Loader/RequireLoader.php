<?php

declare(strict_types=1);

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

final class RequireLoader implements Loader
{
    public function __construct(private string $path)
    {
        $this->path = realpath($this->path) ?: sys_get_temp_dir();
    }

    public function load(MockDefinition $definition): void
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        $temporaryFilePath = sprintf(
            '%s%s%s.php',
            $this->path,
            DIRECTORY_SEPARATOR,
            uniqid('Mockery_')
        );

        file_put_contents($temporaryFilePath, $definition->getCode());

        require $temporaryFilePath;
    }
}
