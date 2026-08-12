<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Loader;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\MockDefinition;
use Mockery\Loader\Loader;
use PHPUnit\Framework\TestCase;
use Throwable;

use function class_exists;
use function random_int;
use function sprintf;

abstract class LoaderTestCase extends TestCase
{
    abstract public function getLoader(): Loader;

    /**
     * @throws Throwable
     */
    public function testLoad(): void
    {
        $className = sprintf('Mock_%s', random_int(1, PHP_INT_MAX));

        $config = new MockConfiguration([], [], [], $className);

        $code = sprintf('<?php class %s {}', $className);

        $definition = new MockDefinition($config, $code);

        $this->getLoader()->load($definition);

        self::assertTrue(class_exists($className));
    }
}
