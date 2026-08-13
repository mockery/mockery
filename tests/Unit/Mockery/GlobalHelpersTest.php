<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\MockInterface;
use Throwable;

use function andAnyOtherArgs;
use function andAnyOthers;
use function anyArgs;
use function get_class;
use function mock;
use function namedMock;
use function spy;
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
        self::assertInstanceOf(AndAnyOtherArgs::class, andAnyOtherArgs());
    }

    /**
     * @throws Throwable
     */
    public function testAndAnyOthers(): void
    {
        self::assertInstanceOf(AndAnyOtherArgs::class, andAnyOthers());
    }

    /**
     * @throws Throwable
     */
    public function testAnyArgs(): void
    {
        self::assertInstanceOf(AnyArgs::class, anyArgs());
    }

    /**
     * @throws Throwable
     */
    public function testMockCreatesAMock(): void
    {
        $mock = mock();

        self::assertInstanceOf(MockInterface::class, $mock);

        try {
            $mock->foo();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Method ' . get_class($mock) . '::foo() does not exist on this mock object',
                $e->getMessage()
            );

            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockCreatesANamedMock(): void
    {
        $className = uniqid('Class');

        $double = namedMock($className);

        self::assertInstanceOf(MockInterface::class, $double);
        self::assertInstanceOf($className, $double);
    }

    /**
     * @throws Throwable
     */
    public function testSpyCreatesASpy(): void
    {
        $double = spy();

        self::assertInstanceOf(MockInterface::class, $double);
        $double->foo();
    }
}
