<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 2/1/16
 * Time: 12:36 PM
 */

use Mockery\MockInterface;
use Mockery\Matcher\PHPUnitConstraint;

class PHPUnitConstraintTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnitConstraint */
    protected $matcher;
    /** @var  PHPUnitConstraint */
    protected $rethrowingMatcher;
    /** @var  MockInterface */
    protected $constraint;

    public function setUp()
    {
        $this->constraint = \Mockery::mock('PHPUnit_Framework_Constraint');
        $this->matcher = new PHPUnitConstraint($this->constraint);
        $this->rethrowingMatcher = new PHPUnitConstraint($this->constraint, true);
    }

    public function testMatches()
    {
        $value1 = 'value1';
        $value2 = 'value1';
        $value3 = 'value1';
        $this->constraint
            ->shouldReceive('evaluate')
            ->once()
            ->with($value1)
            ->getMock()
            ->shouldReceive('evaluate')
            ->once()
            ->with($value2)
            ->andThrow('PHPUnit_Framework_AssertionFailedError')
            ->getMock()
            ->shouldReceive('evaluate')
            ->once()
            ->with($value3)
            ->getMock()
        ;
        $this->assertTrue($this->matcher->match($value1));
        $this->assertFalse($this->matcher->match($value2));
        $this->assertTrue($this->rethrowingMatcher->match($value3));
    }

    /**
     * @expectedException \PHPUnit_Framework_AssertionFailedError
     */
    public function testMatchesWhereNotMatchAndRethrowing()
    {
        $value = 'value';
        $this->constraint
            ->shouldReceive('evaluate')
            ->once()
            ->with($value)
            ->andThrow('PHPUnit_Framework_AssertionFailedError')
        ;
        $this->rethrowingMatcher->match($value);
    }

    public function test__toString()
    {
        $this->assertEquals('<Constraint>', $this->matcher);
    }
}
