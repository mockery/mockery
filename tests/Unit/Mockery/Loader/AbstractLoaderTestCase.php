<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Tests\Unit\Mockery\Loader;

use Mockery\Generator\MockDefinition;
use Mockery\Loader\Loader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractLoaderTestCase extends TestCase
{
    abstract public function getLoader(): Loader;

    public function testLoaderLoadLoadsTheMockDefinition(): void
    {
        $className = uniqid(__FUNCTION__);

        static::assertFalse(class_exists($className));

        $loader = $this->getLoader();

        static::assertInstanceOf(Loader::class, $loader);

        $loader->load($this->createMockDefinition($className));

        static::assertTrue(class_exists($className));

        static::assertTrue(is_a($className, LegacyMockInterface::class, true));
        static::assertTrue(is_a($className, Mock::class, true));
        static::assertTrue(is_a($className, MockInterface::class, true));
    }

    private function createMockDefinition(string $className): MockDefinition
    {
        $mockDefinition = $this->createMock(MockDefinition::class);

        static::assertInstanceOf(MockObject::class, $mockDefinition);

        $mockDefinition->expects(static::once())
            ->method('getClassName')
            ->willReturn($className);

        $mockDefinition->expects(static::once())
            ->method('getCode')
            ->willReturn(sprintf(
                '<?php class %s extends %s implements %s { }',
                $className,
                Mock::class,
                MockInterface::class
            ));

        static::assertInstanceOf(MockDefinition::class, $mockDefinition);

        return $mockDefinition;
    }
}
