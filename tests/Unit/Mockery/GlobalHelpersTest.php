<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Throwable;

use function mock;
use function namedMock;
use function spy;
use function uniqid;

/**
 * @coversDefaultClass \Mockery
 */
final class GlobalHelpersTest extends MockeryTestCase
{
    public function testMockCreatesAMock(): void
    {
        $double = mock();

        self::assertInstanceOf(MockInterface::class, $double);

        $this->expectException(Throwable::class);

        $double->foo();
    }

    public function testNamedMockCreatesANamedMock(): void
    {
        $className = uniqid('Class');

        $double = namedMock($className);

        self::assertInstanceOf(MockInterface::class, $double);
        self::assertInstanceOf($className, $double);
    }

    public function testSpyCreatesASpy(): void
    {
        $double = spy();

        self::assertInstanceOf(MockInterface::class, $double);
        $double->foo();
    }
}
