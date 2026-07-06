<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use PHP73\MockeryTest_ClassMultipleConstructorParams;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class MethodDefinitionPassTest extends TestCase
{
    public function testConstructorDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([MockeryTest_ClassMultipleConstructorParams::class]);
        $pass = new MethodDefinitionPass();
        $code = $pass->apply('class Dave { }', $config);

        $constructor = \mb_substr($code, 0, (int) \mb_strpos($code, 'public function dave'));

        // Returning a value from a constructor is deprecated as of PHP 8.6.
        self::assertNotFalse(\mb_strpos($constructor, 'public function __construct'));
        self::assertNotFalse(\mb_strpos($constructor, '_mockery_handleMethodCall'));
        self::assertFalse(\mb_strpos($constructor, 'return $ret;'));

        // Regular methods still return the handled call value.
        $method = \mb_substr($code, (int) \mb_strpos($code, 'public function dave'));
        self::assertNotFalse(\mb_strpos($method, 'return $ret;'));
    }
}
