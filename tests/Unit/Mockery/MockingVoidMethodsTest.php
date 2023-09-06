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

namespace Mockery\Tests\Unit\Mockery;

use Fixtures\MethodWithVoidReturnType;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockingVoidMethodsTest extends MockeryTestCase
{
    /** @test */
    public function itShouldSuccessfullyBuildTheMock()
    {
        $mock = mock(MethodWithVoidReturnType::class);

        $this->assertInstanceOf(\Fixtures\MethodWithVoidReturnType::class, $mock);
    }

    /** @test */
    public function it_can_stub_and_mock_void_methods()
    {
        $mock = mock(MethodWithVoidReturnType::class);

        $mock->shouldReceive("foo")->once();
        $mock->foo();
    }
}
