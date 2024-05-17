<?php

declare(strict_types=1);

namespace Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Throwable;

final class GlobalHelpersTest extends MockeryTestCase
{
    public function mockeryTestSetUp()
    {
        Mockery::globalHelpers();
    }

    public function mockeryTestTearDown()
    {
        Mockery::close();
    }

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
