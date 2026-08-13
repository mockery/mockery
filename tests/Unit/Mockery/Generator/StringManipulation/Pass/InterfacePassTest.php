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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class InterfacePassTest extends MockeryTestCase
{
    public const CODE = 'class Mock implements MockInterface';

    /**
     * @throws Throwable
     */
    public function testShouldAddAnyInterfaceNamesToImplementsDefinition(): void
    {
        $pass = new InterfacePass();

        $config = Mockery::mock(MockConfiguration::class, [
            'getTargetInterfaces' => [
                Mockery::mock([
                    'getName' => '\Dave\Dave',
                ]),
                Mockery::mock([
                    'getName' => '\Paddy\Paddy',
                ]),
            ],
        ]);

        $code = $pass->apply(self::CODE, $config);

        self::assertNotFalse(mb_strpos($code, 'implements MockInterface, \Dave\Dave, \Paddy\Paddy'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotAlterCodeIfNoTargetInterfaces(): void
    {
        $pass = new InterfacePass();

        $config = Mockery::mock(MockConfiguration::class, [
            'getTargetInterfaces' => [],
        ]);

        $code = $pass->apply(self::CODE, $config);
        self::assertSame(self::CODE, $code);
    }
}
