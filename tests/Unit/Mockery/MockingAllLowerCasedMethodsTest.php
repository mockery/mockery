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
use PHP73\ClassWithAllLowerCaseMethod;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingAllLowerCasedMethodsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItShouldAllowToCallAllLowerCasedMethodAsCamelCased(): void
    {
        $expected = 'mocked';

        $mock = mock(ClassWithAllLowerCaseMethod::class);

        $mock->shouldReceive('userExpectsCamelCaseMethod')
            ->andReturn($expected);

        self::assertSame($expected, $mock->userExpectsCamelCaseMethod());
    }
}
