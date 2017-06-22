<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
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

use Mockery\Adapter\Phpunit\MockeryTestCase;

class Mockery_MockTest extends MockeryTestCase
{
    public function testAnonymousMockWorksWithNotAllowingMockingOfNonExistentMethods()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock();
        $m->shouldReceive("test123")->andReturn(true);
        assertThat($m->test123(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testMockWithNotAllowingMockingOfNonExistentMethodsCanBeGivenAdditionalMethodsToMockEvenIfTheyDontExistOnClass()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock('ExampleClassForTestingNonExistentMethod');
        $m->shouldAllowMockingMethod('testSomeNonExistentMethod');
        $m->shouldReceive("testSomeNonExistentMethod")->andReturn(true);
        assertThat($m->testSomeNonExistentMethod(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testShouldAllowMockingMethodReturnsMockInstance()
    {
        $m = Mockery::mock('someClass');
        $this->assertInstanceOf('Mockery\MockInterface', $m->shouldAllowMockingMethod('testFunction'));
    }

    public function testShouldAllowMockingProtectedMethodReturnsMockInstance()
    {
        $m = Mockery::mock('someClass');
        $this->assertInstanceOf('Mockery\MockInterface', $m->shouldAllowMockingProtectedMethods('testFunction'));
    }

    public function testMockAddsToString()
    {
        $mock = mock('ClassWithNoToString');
        $this->assertTrue(method_exists($mock, '__toString'));
    }

    public function testMockToStringMayBeDeferred()
    {
        $mock = mock('ClassWithToString')->shouldDeferMissing();
        $this->assertEquals("foo", (string)$mock);
    }

    public function testMockToStringShouldIgnoreMissingAlwaysReturnsString()
    {
        $mock = mock('ClassWithNoToString')->shouldIgnoreMissing();
        $this->assertNotEquals('', (string)$mock);

        $mock->asUndefined();
        $this->assertNotEquals('', (string)$mock);
    }

    public function testShouldIgnoreMissing()
    {
        $mock = mock('ClassWithNoToString')->shouldIgnoreMissing();
        $this->assertNull($mock->nonExistingMethod());
    }

    /**
     * @expectedException Mockery\Exception
     */
    public function testShouldIgnoreMissingDisallowMockingNonExistentMethodsUsingGlobalConfiguration()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock('ClassWithMethods')->shouldIgnoreMissing();
        $mock->shouldReceive('nonExistentMethod');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testShouldIgnoreMissingCallingNonExistentMethodsUsingGlobalConfiguration()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock('ClassWithMethods')->shouldIgnoreMissing();
        $mock->nonExistentMethod();
    }

    public function testShouldIgnoreMissingCallingExistentMethods()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock('ClassWithMethods')->shouldIgnoreMissing();
        assertThat(nullValue($mock->foo()));
        $mock->shouldReceive('bar')->passthru();
        assertThat($mock->bar(), equalTo('bar'));
    }

    public function testShouldIgnoreMissingCallingNonExistentMethods()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        $mock = mock('ClassWithMethods')->shouldIgnoreMissing();
        assertThat(nullValue($mock->foo()));
        assertThat(nullValue($mock->bar()));
        assertThat(nullValue($mock->nonExistentMethod()));

        $mock->shouldReceive(array('foo' => 'new_foo', 'nonExistentMethod' => 'result'));
        $mock->shouldReceive('bar')->passthru();
        assertThat($mock->foo(), equalTo('new_foo'));
        assertThat($mock->bar(), equalTo('bar'));
        assertThat($mock->nonExistentMethod(), equalTo('result'));
    }

    public function testCanMockException()
    {
        $exception = Mockery::mock('Exception');
        $this->assertInstanceOf('Exception', $exception);
    }

    public function testCanMockSubclassOfException()
    {
        $errorException = Mockery::mock('ErrorException');
        $this->assertInstanceOf('ErrorException', $errorException);
        $this->assertInstanceOf('Exception', $errorException);
    }

    public function testCallingShouldReceiveWithoutAValidMethodName()
    {
        $mock = Mockery::mock();

        $this->expectException("InvalidArgumentException", "Received empty method name");
        $mock->shouldReceive("");
    }

    /**
     * @expectedException Mockery\Exception
     */
    public function testShouldThrowExceptionWithInvalidClassName()
    {
        mock('ClassName.CannotContainDot');
    }
}


class ExampleClassForTestingNonExistentMethod
{
}

class ClassWithToString
{
    public function __toString()
    {
        return 'foo';
    }
}

class ClassWithNoToString
{
}

class ClassWithMethods
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }
}
