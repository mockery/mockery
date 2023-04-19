<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace MockeryTest\Unit\Mockery;

use MockeryTest\Mockery\Fixtures\MethodWithNullableReturnType;
use MockeryTest\Mockery\Fixtures\MethodWithParametersWithDefaultValues;

class MockingMethodsWithNullableParametersTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @test */
    public function it_can_handle_nullable_typed_parameters()
    {
        $mock = \mock(\MockeryTest\Mockery\MethodWithNullableTypedParameter::class);

        $this->assertInstanceOf(\MockeryTest\Mockery\MethodWithNullableTypedParameter::class, $mock);
    }

    /** @test */
    public function it_can_handle_default_parameters()
    {
        $mock = \mock(\MockeryTest\Fixture\MethodWithParametersWithDefaultValues::class);

        $this->assertInstanceOf(\MockeryTest\Fixture\MethodWithParametersWithDefaultValues::class, $mock);
    }
}
