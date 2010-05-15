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

class ExpectationTest extends PHPUnit_Framework_TestCase
{

    public function setup ()
    {
        $this->mock = \Mockery::mock('foo');
    }
    
    public function teardown()
    {
        \Mockery::close();
    }

    public function testReturnsNullWhenNoArgs()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo());
    }
    
    public function testReturnsNullWhenSingleArg()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo(1));
    }
    
    public function testReturnsNullWhenManyArgs()
    {
        $this->mock->shouldReceive('foo');
        $this->assertNull($this->mock->foo('foo', array(), new stdClass));
    }
    
    public function testReturnsSameValueForAllIfNoArgsExpectationAndNoneGiven()
    {
        $this->mock->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $this->mock->foo());
    }
    
    public function testReturnsSameValueForAllIfNoArgsExpectationAndSomeGiven()
    {
        $this->mock->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $this->mock->foo('foo'));
    }
    
    public function testReturnsValueFromSequenceSequentially()
    {
        $this->mock->shouldReceive('foo')->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        $this->assertEquals(2, $this->mock->foo('foo'));
    }
    
    public function testReturnsValueFromSequenceSequentiallyAndRepeatedlyReturnsFinalValueOnExtraCalls()
    {
        $this->mock->shouldReceive('foo')->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        $this->assertEquals(3, $this->mock->foo('foo'));
        $this->assertEquals(3, $this->mock->foo('foo'));
    }
    
    public function testReturnsValueFromSequenceSequentiallyAndRepeatedlyReturnsFinalValueOnExtraCallsWithManyAndReturnCalls()
    {
        $this->mock->shouldReceive('foo')->andReturn(1)->andReturn(2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        $this->assertEquals(3, $this->mock->foo('foo'));
        $this->assertEquals(3, $this->mock->foo('foo'));
    }

    public function testReturnsValueOfClosure()
    {
        $this->mock->shouldReceive('foo')->with(5)->andReturn(function($v){return $v+1;});
        $this->assertEquals(6, $this->mock->foo(5));
    }
    
    public function testReturnsUndefined()
    {
        $this->mock->shouldReceive('foo')->andReturnUndefined();
        $this->assertTrue($this->mock->foo() instanceof \Mockery\Undefined);
    }
    
    /**
     * @expectedException OutOfBoundsException
     */
    public function testThrowsException()
    {
        $this->mock->shouldReceive('foo')->andThrow(new OutOfBoundsException);
        $this->mock->foo();
    }
    
    /**
     * @expectedException OutOfBoundsException
     */
    public function testThrowsExceptionSequentially()
    {
        $this->mock->shouldReceive('foo')->andThrow(new Exception)->andThrow(new OutOfBoundsException);
        try {
            $this->mock->foo();
        } catch (Exception $e) {}
        $this->mock->foo();
    }
    
    public function testMultipleExpectationsWithReturns()
    {
        $this->mock->shouldReceive('foo')->with(1)->andReturn(10);
        $this->mock->shouldReceive('bar')->with(2)->andReturn(20);
        $this->assertEquals(10, $this->mock->foo(1));
        $this->assertEquals(20, $this->mock->bar(2));
    }
    
    public function testExpectsNoArguments()
    {
        $this->mock->shouldReceive('foo')->withNoArgs();
        $this->mock->foo();
    }
    
    /**
     * @expectedException \Mockery\Exception
     * @group 1A
     */
    public function testExpectsNoArgumentsThrowsExceptionIfAnyPassed()
    {
        $this->mock->shouldReceive('foo')->withNoArgs();
        $this->mock->foo(1);
    }
    
    public function testExpectsAnyArguments()
    {
        $this->mock->shouldReceive('foo')->withAnyArgs();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 'k', new stdClass);
    }
    
    public function testUsesMockeryScalarConstantPlaceholdersForAnyArgument() //and all scalars
    {
        $this->markTestIncomplete();
        $this->mock->shouldReceive('foo')->with(\Mockery::ANY);
    }
    
    public function testExpectsArgumentMatchingRegularExpression()
    {
        $this->mock->shouldReceive('foo')->with('/bar/i');
        $this->mock->foo('xxBARxx');
    }
    
    public function testExpectsArgumentMatchingObjectType()
    {
        $this->mock->shouldReceive('foo')->with('\stdClass');
        $this->mock->foo(new stdClass);
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testThrowsExceptionOnNoArgumentMatch()
    {
        $this->mock->shouldReceive('foo')->with(1);
        $this->mock->foo(2);
    }
    
    public function testNeverCalled()
    {
        $this->mock->shouldReceive('foo')->never();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testNeverCalledThrowsExceptionOnCall()
    {
        $this->mock->shouldReceive('foo')->never();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledOnce()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledOnceThrowsExceptionIfNotCalled()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledOnceThrowsExceptionIfCalledTwice()
    {
        $this->mock->shouldReceive('foo')->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledTwice()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledTwiceThrowsExceptionIfNotCalled()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledOnceThrowsExceptionIfCalledThreeTimes()
    {
        $this->mock->shouldReceive('foo')->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledZeroOrMoreTimesAtZeroCalls()
    {
        $this->mock->shouldReceive('foo')->zeroOrMoreTimes();
        $this->mock->mockery_verify();
    }
    
    public function testCalledZeroOrMoreTimesAtThreeCalls()
    {
        $this->mock->shouldReceive('foo')->zeroOrMoreTimes();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testTimesCountCalls()
    {
        $this->mock->shouldReceive('foo')->times(4);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testTimesCountCallThrowsExceptionOnTooFewCalls()
    {
        $this->mock->shouldReceive('foo')->times(2);
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testTimesCountCallThrowsExceptionOnTooManyCalls()
    {
        $this->mock->shouldReceive('foo')->times(2);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledAtLeastOnceAtExactlyOneCall()
    {
        $this->mock->shouldReceive('foo')->atLeast()->once();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledAtLeastOnceAtExactlyThreeCalls()
    {
        $this->mock->shouldReceive('foo')->atLeast()->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledAtLeastThrowsExceptionOnTooFewCalls()
    {
        $this->mock->shouldReceive('foo')->atLeast()->twice();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledAtMostOnceAtExactlyOneCall()
    {
        $this->mock->shouldReceive('foo')->atMost()->once();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testCalledAtMostAtExactlyThreeCalls()
    {
        $this->mock->shouldReceive('foo')->atMost()->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCalledAtLeastThrowsExceptionOnTooManyCalls()
    {
        $this->mock->shouldReceive('foo')->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testExactCountersOverrideAnyPriorSetNonExactCounters()
    {
        $this->mock->shouldReceive('foo')->atLeast()->once()->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testComboOfLeastAndMostCallsWithOneCall()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->mock->mockery_verify(); 
    }
    
    public function testComboOfLeastAndMostCallsWithTwoCalls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify(); 
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testComboOfLeastAndMostCallsThrowsExceptionAtTooFewCalls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->mockery_verify(); 
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testComboOfLeastAndMostCallsThrowsExceptionAtTooManyCalls()
    {
        $this->mock->shouldReceive('foo')->atleast()->once()->atMost()->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->mockery_verify(); 
    }
    
    public function testCallCountingOnlyAppliesToMatchedExpectations()
    {
        $this->mock->shouldReceive('foo')->with(1)->once();
        $this->mock->shouldReceive('foo')->with(2)->twice();
        $this->mock->shouldReceive('foo')->with(3);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testCallCountingThrowsExceptionOnAnyMismatch()
    {
        $this->mock->shouldReceive('foo')->with(1)->once();
        $this->mock->shouldReceive('foo')->with(2)->twice();
        $this->mock->shouldReceive('foo')->with(3);
        $this->mock->shouldReceive('bar');
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->bar();
        $this->mock->mockery_verify();
    }
    
    public function testOrderedCallsWithoutError()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testOrderedCallsWithOutOfOrderError()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testDifferentArgumentsAndOrderingsPassWithoutException()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2)->ordered();
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testDifferentArgumentsAndOrderingsThrowExceptionWhenInWrongOrder()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2)->ordered();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->mock->mockery_verify();
    }
    
    public function testUnorderedCallsIgnoredForOrdering()
    {
        $this->mock->shouldReceive('foo')->with(1)->ordered();
        $this->mock->shouldReceive('foo')->with(2);
        $this->mock->shouldReceive('foo')->with(3)->ordered();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->foo(2);
        $this->mock->mockery_verify();
    }
    
    public function testOrderingOfDefaultGrouping()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->mockery_verify();
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testOrderingOfDefaultGroupingThrowsExceptionOnWrongOrder()
    {
        $this->mock->shouldReceive('foo')->ordered();
        $this->mock->shouldReceive('bar')->ordered();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testOrderingUsingNumberedGroups()
    {
        $this->mock->shouldReceive('start')->ordered(1);
        $this->mock->shouldReceive('foo')->ordered(2);
        $this->mock->shouldReceive('bar')->ordered(2);
        $this->mock->shouldReceive('final')->ordered();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
        $this->mock->mockery_verify();
    }
    
    public function testOrderingUsingNamedGroups()
    {
        $this->mock->shouldReceive('start')->ordered('start');
        $this->mock->shouldReceive('foo')->ordered('foobar');
        $this->mock->shouldReceive('bar')->ordered('foobar');
        $this->mock->shouldReceive('final')->ordered();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
        $this->mock->mockery_verify();
    }
    
    public function testGroupedUngroupedOrderingDoNotOverlap()
    {
        $s = $this->mock->shouldReceive('start')->ordered();
        $m = $this->mock->shouldReceive('mid')->ordered('foobar');
        $e = $this->mock->shouldReceive('end')->ordered();
        $this->assertTrue($s->getOrderNumber() < $m->getOrderNumber());
        $this->assertTrue($m->getOrderNumber() < $e->getOrderNumber());
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testGroupedOrderingThrowsExceptionWhenCallsDisordered()
    {
        $this->mock->shouldReceive('foo')->ordered('first');
        $this->mock->shouldReceive('bar')->ordered('second');
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testExpectationMatchingWithNoArgsOrderings()
    {
        $this->mock->shouldReceive('foo')->withNoArgs()->once()->ordered();
        $this->mock->shouldReceive('bar')->withNoArgs()->once()->ordered();
        $this->mock->shouldReceive('foo')->withNoArgs()->once()->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testExpectationMatchingWithAnyArgsOrderings()
    {
        $this->mock->shouldReceive('foo')->withAnyArgs()->once()->ordered();
        $this->mock->shouldReceive('bar')->withAnyArgs()->once()->ordered();
        $this->mock->shouldReceive('foo')->withAnyArgs()->once()->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->mockery_verify();
    }
    
    public function testEnsuresOrderingIsNotCrossMockByDefault()
    {
        $this->markTestIncomplete('Pending mock containers');
        $this->mock->shouldReceive('foo')->ordered();
        $mock2 = \Mockery::mock('bar'); // need parent container for mocks
        $mock2->shouldReceive('bar')->ordered();
        $mock2->bar();
        $this->mock->foo();
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testEnsuresOrderingIsCrossMockWhenGloballyFlagSet()
    {
        $this->markTestIncomplete('Pending mock containers');
        $this->mock->shouldReceive('foo')->globally()->ordered();
        $mock2 = \Mockery::mock('bar'); // need parent container for mocks
        $mock2->shouldReceive('bar')->globally()->ordered();
        $mock2->bar();
        $this->mock->foo();
    }
    
    public function testExpectationCastToStringFormatting()
    {
        $exp = $this->mock->shouldReceive('foo')->with(1, 'bar', new stdClass, array());
        $this->assertEquals('foo(1, "bar", stdClass, Array)', (string) $exp);
    }
    
    public function testMultipleExpectationCastToStringFormatting()
    {
        $this->markTestIncomplete('Need composite expectations');
        $exp = $this->mock->shouldReceive('foo', 'bar')->with(1);
        $this->assertEquals('[foo(1), bar(1)]', (string) $exp);
    }
    
    public function testGroupedOrderingWithLimitsAllowsMultipleReturnValues()
    {
        $this->mock->shouldReceive('foo')->with(2)->once()->andReturn('first');
        $this->mock->shouldReceive('foo')->with(2)->twice()->andReturn('second/third');
        $this->mock->shouldReceive('foo')->with(2)->andReturn('infinity');
        $this->assertEquals('first', $this->mock->foo(2));
        $this->assertEquals('second/third', $this->mock->foo(2));
        $this->assertEquals('second/third', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->assertEquals('infinity', $this->mock->foo(2));
        $this->mock->mockery_verify();
    }
    
    public function testExpectationsCanBeMarkedAsDefaults()
    {
        $this->mock->shouldReceive('foo')->andReturn('bar')->byDefault();
        $this->assertEquals('bar', $this->mock->foo());
    }

}
