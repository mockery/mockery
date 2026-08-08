<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use PHP86\ClassWithConstructorAndDestructor;
use Tests\Unit\AbstractTestCase;
use Throwable;

/**
 * @coversDefaultClass \Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass
 */
final class MethodDefinitionPassTest extends AbstractTestCase
{
    /** @throws Throwable */
    public function testConstructMethodDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([ClassWithConstructorAndDestructor::class]);

        $pass = new MethodDefinitionPass();

        self::assertStringContainsString(
            implode(
                "\n",
                [
                    'public function __construct(int $number){',
                    '$argc = func_num_args();',
                    '$argv = func_get_args();',
                    '$this->_mockery_handleMethodCall(__FUNCTION__, $argv);',
                    '}',
                ]
            ),
            $pass->apply('class Example {}', $config)
        );
    }

    /** @throws Throwable */
    public function testDestructMethodDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([ClassWithConstructorAndDestructor::class]);

        $pass = new MethodDefinitionPass();

        self::assertStringContainsString(
            implode(
                "\n",
                [
                    'public function __destruct(){',
                    '$argc = func_num_args();',
                    '$argv = func_get_args();',
                    '$this->_mockery_handleMethodCall(__FUNCTION__, $argv);',
                    '}',
                ]
            ),
            $pass->apply('class Example {}', $config)
        );
    }

    /** @throws Throwable */
    public function testNumberMethodDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([ClassWithConstructorAndDestructor::class]);

        $pass = new MethodDefinitionPass();

        self::assertStringContainsString(
            implode(
                "\n",
                [
                    'public function number(): int{',
                    '$argc = func_num_args();',
                    '$argv = func_get_args();',
                    '$ret = $this->_mockery_handleMethodCall(__FUNCTION__, $argv);',
                    'return $ret;',
                    '}',
                ]
            ),
            $pass->apply('class Example {}', $config)
        );
    }

    /**
     * @requires PHP 8.1.0-dev
     * @throws Throwable
     */
    public function testNeverMethodDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([ClassWithConstructorAndDestructor::class]);

        $pass = new MethodDefinitionPass();

        self::assertStringContainsString(
            implode(
                "\n",
                [
                    'public function neverMethod(): never{',
                    '$argc = func_num_args();',
                    '$argv = func_get_args();',
                    '$this->_mockery_handleMethodCall(__FUNCTION__, $argv);',
                    '}',
                ]
            ),
            $pass->apply('class Example {}', $config)
        );
    }

    /** @throws Throwable */
    public function testVoidMethodDoesNotReturnAValue(): void
    {
        $config = new MockConfiguration([ClassWithConstructorAndDestructor::class]);

        $pass = new MethodDefinitionPass();

        self::assertStringContainsString(
            implode(
                "\n",
                [
                    'public function voidMethod(): void{',
                    '$argc = func_num_args();',
                    '$argv = func_get_args();',
                    '$this->_mockery_handleMethodCall(__FUNCTION__, $argv);',
                    '}',
                ]
            ),
            $pass->apply('class Example {}', $config)
        );
    }
}
