<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;

class CallTypeHintPassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = ' public function __call($method, array $args) {}
                   public static function __callStatic($method, array $args) {}
    ';

    /**
     * @test
     */
    public function shouldRemoveCallTypeHintIfRequired()
    {
        $pass = new CallTypeHintPass;
        $config = m::mock("Mockery\Generator\MockConfiguration", array(
            "requiresCallTypeHintRemoval" => true,
        ))->shouldDeferMissing();
        $code = $pass->apply(static::CODE, $config);
        $this->assertContains('__call($method, $args)', $code);
    }

    /**
     * @test
     */
    public function shouldRemoveCallStaticTypeHintIfRequired()
    {
        $pass = new CallTypeHintPass;
        $config = m::mock("Mockery\Generator\MockConfiguration", array(
            "requiresCallStaticTypeHintRemoval" => true,
        ))->shouldDeferMissing();
        $code = $pass->apply(static::CODE, $config);
        $this->assertContains('__callStatic($method, $args)', $code);
    }
}
