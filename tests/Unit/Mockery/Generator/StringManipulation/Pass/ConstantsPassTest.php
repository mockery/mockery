<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

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

    /**
     * @throws Throwable
     */
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
            ],
        );

        $code = $pass->apply(self::CODE, $config);

        self::assertNotFalse(mb_strpos($code, "const FOO = 'test'"));
    }
}
