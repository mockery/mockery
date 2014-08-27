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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

class RecorderTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testRecorderWithSimpleObject()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
        });

        $this->assertEquals(1, $mock->foo());
        $mock->mockery_verify();
    }

    public function testArgumentsArePassedAsMethodExpectations()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    public function testArgumentsLooselyMatchedByDefault()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar('2'));
        $mock->mockery_verify();
    }

    public function testMultipleMethodExpectations()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $this->assertEquals(1, $mock->foo());
        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    public function testRecordingDoesNotSpecifyExactOrderByDefault()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $this->assertEquals(4, $mock->bar(2));
        $this->assertEquals(1, $mock->foo());
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function testRecordingDoesSpecifyExactOrderInStrictMode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doFoo();
            $user->doBar();
        });

        $mock->bar(2);
        $mock->foo();
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function testArgumentsAreMatchedExactlyUnderStrictMode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar('2');
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function testThrowsExceptionWhenArgumentsNotExpected()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(4);
    }

    public function testCallCountUnconstrainedByDefault()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(2);
        $this->assertEquals(4, $mock->bar(2));
        $mock->mockery_verify();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCallCountConstrainedInStrictMode()
    {
        $mock = $this->container->mock(new MockeryTestSubject);
        $mock->shouldExpect(function ($subject) {
            $subject->shouldBeStrict();
            $user = new MockeryTestSubjectUser($subject);
            $user->doBar();
        });

        $mock->bar(2);
        $mock->bar(2);
        $mock->mockery_verify();
    }

}

class MockeryTestSubject
{
    public function foo() { return 1; }
    public function bar($i) { return $i * 2; }
}

class MockeryTestSubjectUser
{
    public $subject = null;
    public function __construct($subject) { $this->subject = $subject; }
    public function doFoo() { return $this->subject->foo(); }
    public function doBar() { return $this->subject->bar(2); }
}
