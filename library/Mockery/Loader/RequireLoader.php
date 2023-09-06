<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;
use RuntimeException;

use const DIRECTORY_SEPARATOR;

/**
 * @see \Mockery\Tests\Unit\Mockery\LoaderTest
 */
final class RequireLoader implements Loader
{
    /**
     * @var string
     */
    private $lastPath;

    /**
     * @var string
     */
    private $path;

    public function __construct(?string $path = null)
    {
        $this->lastPath = $this->path = realpath($path ?? sys_get_temp_dir()) ?: sys_get_temp_dir();

        register_shutdown_function([$this, '__destruct']);
    }

    public function __destruct()
    {
        $files = array_diff(
            glob($this->path . DIRECTORY_SEPARATOR . 'Mockery_*.php') ?: [],
            [$this->lastPath]
        );

        foreach ($files as $file) {
            if (! is_file($file)) {
                continue;
            }

            @unlink($file);
        }
    }

    public function load(MockDefinition $definition): void
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        $lastPath = &$this->lastPath;

        $lastPath = sprintf('%s%s%s.php', $this->path, DIRECTORY_SEPARATOR, uniqid('Mockery_'));

        $saved = file_put_contents($lastPath, $definition->getCode());

        if (false === $saved) {
            throw new RuntimeException(sprintf('Unable to write file "%s"', $lastPath));
        }

        if (file_exists($lastPath)) {
            require $lastPath;
        }
    }
}
