<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\ClassWithConstants;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingClassConstantsTest extends MockeryTestCase
{
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
