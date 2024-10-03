<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Loader;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\MockDefinition;
use Mockery\Loader\Loader;
use PHPUnit\Framework\TestCase;

abstract class LoaderTestCase extends TestCase
{
    abstract public function getLoader(): Loader;

    public function testLoad(): void
    {
        $className = \uniqid('Mock_', false);

        $config = new MockConfiguration([], [], [], $className);

        $code = "<?php class {$className} { } ";

        $definition = new MockDefinition($config, $code);

        $this->getLoader()
            ->load($definition);

        self::assertTrue(\class_exists($className));
    }
}
