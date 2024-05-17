<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\ClassWithAllLowerCaseMethod;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingAllLowerCasedMethodsTest extends MockeryTestCase
{
    public function testItShouldAllowToCallAllLowerCasedMethodAsCamelCased(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

        $expected = 'mocked';

        $mock = mock(ClassWithAllLowerCaseMethod::class);

        $mock->shouldReceive('userExpectsCamelCaseMethod')
            ->andReturn($expected);

        self::assertSame($expected, $mock->userExpectsCamelCaseMethod());
    }
}
