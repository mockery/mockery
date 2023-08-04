<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;
use Mockery\Loader\Loader;

class RequireLoader implements Loader
{
    protected $path;

    public function __construct($path = null)
    {
        $this->path = realpath($path) ?: sys_get_temp_dir();
    }

    public function __destruct()
    {
        foreach (glob($this->path . DIRECTORY_SEPARATOR . 'Mockery_*.php') as $file) {
            @unlink($file);
        }
    }

    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        $fileName = sprintf('%s%s%s.php', $this->path, DIRECTORY_SEPARATOR, uniqid('Mockery_'));

        file_put_contents($fileName, $definition->getCode());

        require $fileName;
    }
}
