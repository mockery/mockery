<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class InterfacePassTest extends TestCase
{
    public const CODE = 'class Mock implements MockInterface';

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

        self::assertNotFalse(\mb_strpos($code, 'implements MockInterface, \Dave\Dave, \Paddy\Paddy'));
    }

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
