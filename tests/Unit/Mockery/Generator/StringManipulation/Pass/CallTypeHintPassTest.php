<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class CallTypeHintPassTest extends TestCase
{
    public const CODE = ' public function __call($method, array $args) {}
                   public static function __callStatic($method, array $args) {}
    ';

    public function testShouldRemoveCallStaticTypeHintIfRequired(): void
    {
        $pass = new CallTypeHintPass();
        $config = Mockery::mock(MockConfiguration::class, [
            'requiresCallStaticTypeHintRemoval' => true,
        ])->makePartial();
        $code = $pass->apply(self::CODE, $config);
        self::assertNotFalse(\mb_strpos($code, '__callStatic($method, $args)'));
    }

    public function testShouldRemoveCallTypeHintIfRequired(): void
    {
        $pass = new CallTypeHintPass();
        $config = Mockery::mock(MockConfiguration::class, [
            'requiresCallTypeHintRemoval' => true,
        ])->makePartial();
        $code = $pass->apply(self::CODE, $config);
        self::assertNotFalse(\mb_strpos($code, '__call($method, $args)'));
    }
}
