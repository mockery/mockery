<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ConstantsPass;
use PHP73\ClassWithConstants;
use PHPUnit\Framework\TestCase;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class ConstantsPassTest extends TestCase
{
    public const CODE = 'class Foo {}';

    public function testShouldAddConstants(): void
    {
        $pass = new ConstantsPass();

        $config = new MockConfiguration(
            [],
            [],
            [],
            ClassWithConstants::class,
            false,
            [],
            false,
            [
                ClassWithConstants::class => [
                    'FOO' => 'test',
                ],
            ]
        );

        $code = $pass->apply(static::CODE, $config);

        self::assertNotFalse(mb_strpos($code, "const FOO = 'test'"));
    }
}
