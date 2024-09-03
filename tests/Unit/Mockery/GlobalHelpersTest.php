<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\MockInterface;
use Throwable;

use function uniqid;

/**
 * @coversDefaultClass \Mockery
 */
final class GlobalHelpersTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testAndAnyOtherArgs(): void
    {
        self::assertInstanceOf(AndAnyOtherArgs::class, \andAnyOtherArgs());
    }

    /**
     * @throws Throwable
     */
    public function testAndAnyOthers(): void
    {
        self::assertInstanceOf(AndAnyOtherArgs::class, \andAnyOthers());
    }

    /**
     * @throws Throwable
     */
    public function testAnyArgs(): void
    {
        self::assertInstanceOf(AnyArgs::class, \anyArgs());
    }

    public function testMockCreatesAMock(): void
    {
        $double = \mock();

        self::assertInstanceOf(MockInterface::class, $double);

        $this->expectException(Throwable::class);

        $double->foo();
    }

    public function testNamedMockCreatesANamedMock(): void
    {
        $className = uniqid('Class');

        $double = \namedMock($className);

        self::assertInstanceOf(MockInterface::class, $double);
        self::assertInstanceOf($className, $double);
    }

    public function testSpyCreatesASpy(): void
    {
        $double = \spy();

        self::assertInstanceOf(MockInterface::class, $double);
        $double->foo();
    }
}
