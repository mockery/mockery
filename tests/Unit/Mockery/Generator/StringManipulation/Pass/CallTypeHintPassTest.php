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
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use Throwable;

use function mb_strpos;

/**
 * @coversDefaultClass \Mockery
 */
final class CallTypeHintPassTest extends MockeryTestCase
{
    public const CODE = ' public function __call($method, array $args) {}
                   public static function __callStatic($method, array $args) {}
    ';

    /**
     * @throws Throwable
     */
    public function testShouldRemoveCallStaticTypeHintIfRequired(): void
    {
        $pass = new CallTypeHintPass();
        $config = Mockery::mock(MockConfiguration::class, [
            'requiresCallStaticTypeHintRemoval' => true,
        ])->makePartial();
        $code = $pass->apply(self::CODE, $config);
        self::assertNotFalse(mb_strpos($code, '__callStatic($method, $args)'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldRemoveCallTypeHintIfRequired(): void
    {
        $pass = new CallTypeHintPass();
        $config = Mockery::mock(MockConfiguration::class, [
            'requiresCallTypeHintRemoval' => true,
        ])->makePartial();
        $code = $pass->apply(self::CODE, $config);
        self::assertNotFalse(mb_strpos($code, '__call($method, $args)'));
    }
}
