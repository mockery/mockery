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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\ClassWithConstants;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingClassConstantsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItShouldAllowToMockClassConstants(): void
    {
        Mockery::getConfiguration()->setConstantsMap([
            ClassWithConstants::class => [
                'FOO' => 'baz',
                'X' => 2,
                'BAZ' => [
                    'qux' => 'daz',
                ],
            ],
        ]);

        $mock = Mockery::mock('overload:' . ClassWithConstants::class);

        self::assertSame('baz', $mock::FOO);
        self::assertSame(2, $mock::X);
        self::assertSame([
            'qux' => 'daz',
        ], $mock::BAZ);
    }
}
