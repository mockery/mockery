<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Mockery\Generator\MockConfiguration;

class InterfacePassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = "class Mock implements MockInterface";

    /**
     * @test
     */
    public function shouldNotAlterCodeIfNoTargetInterfaces()
    {
        $pass = new InterfacePass;

        $config = m::mock("Mockery\Generator\MockConfiguration", array(
            "getTargetInterfaces" => array(),
        ));

        $code = $pass->apply(static::CODE, $config);
        $this->assertEquals(static::CODE, $code);
    }

    /**
     * @test
     */
    public function shouldAddAnyInterfaceNamesToImplementsDefinition()
    {
        $pass = new InterfacePass;

        $config = m::mock("Mockery\Generator\MockConfiguration", array(
            "getTargetInterfaces" => array(
                m::mock(array("getName" => "Dave\Dave")),
                m::mock(array("getName" => "Paddy\Paddy")),
            ),
        ));

        $code = $pass->apply(static::CODE, $config);

        $this->assertContains("implements MockInterface, \Dave\Dave, \Paddy\Paddy", $code);
    }

}
