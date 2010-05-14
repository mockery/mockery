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
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */
 
namespace Mockery;

class Expectation
{

    /**
     * Mock object to which this expectation belongs
     *
     * @var object
     */
    protected $_mock = null;
    
    /**
     * Method name 
     *
     * @var string
     */
    protected $_name = null;
    
    /**
     * Arguments expected by this expectation
     *
     * @var array
     */
    protected $_expectedArgs = array();
    
    /**
     * Count validator store
     *
     * @var array
     */
    protected $_countValidators = array();
    
    /**
     * The count validator class to use
     *
     * @var string
     */
    protected $_countValidatorClass = 'Mockery\CountValidator\Exact';
    
    /**
     * Actual count of calls to this expectation
     *
     * @var int
     */
    protected $_actualCount = 0;
    
    /**
     * Value to return from this expectation
     *
     * @var mixed
     */
    protected $_returnValue = null;
    
    /**
     * Array of return values as a queue for multiple return sequence
     *
     * @var array
     */
    protected $_returnQueue = array();
    
    /**
     * Integer representing the call order of this expectation
     *
     * @var int
     */
    protected $_orderNumber = null;
    
    /**
     * Integer representing the call order of this expectation on a global basis
     *
     * @var int
     */
    protected $_globalOrderNumber = null;
    
    /**
     * Flag indicating that an exception is expected to be throw (not returned)
     *
     * @var bool
     */
    protected $_throw = false;
    
    /**
     * Flag indicating whether the order of calling is determined locally or
     * globally
     *
     * @var bool
     */
    protected $_globally = false;
    
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct(\Mockery\Mock $mock, $name)
    {
        $this->_mock = $mock;
        $this->_name = $name;
    }
    
    /**
     * Return a string with the method name and arguments formatted
     *
     * @param string $name Name of the expected method
     * @param array $args List of arguments to the method
     * @return string
     */
    public function __toString()
    {
        return \Mockery::formatArgs($this->_expectedArgs);
    }
    
    /**
     * Verify the current call, i.e. that the given arguments match those
     * of this expectation
     *
     * @param array $args
     * @return mixed
     */
    public function verifyCall(array $args)
    {
        $this->validateOrder();
        $this->_actualCount++;
        $return = $this->_getReturnValue($args);
        if ($return instanceof \Exception && $this->_throw === true) {
            throw $return;
        }
        return $return; 
    }
    
    /**
     * Fetch the return value for the matching args
     *
     * @param array $args
     * @return mixed
     */
    protected function _getReturnValue(array $args)
    {
        return array_shift($this->_returnQueue);
    }
    
    /**
     * Checks if this expectation is eligible for additional calls
     *
     * @return bool
     */
    public function isEligible()
    {
        foreach ($this->_countValidators as $validator) {
            if (!$validator->isEligible($this->_actualCount)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if there is a constraint on call count
     *
     * @return bool
     */
    public function isCallCountConstrained()
    {
        return (count($this->_countValidators) > 0);
    }
    
    /**
     * Verify call order
     *
     * @return void
     */
    public function validateOrder()
    {
        if ($this->_orderNumber) {
            $this->_mock->mockery_validateOrder((string) $this, $this->_orderNumber);
        }
        if ($this->_globalOrderNumber) {
            $this->_mock->mockery_validateOrder((string) $this, $this->_globalOrderNumber);
        }
    }
    
    /**
     * Verify this expectation
     *
     * @return bool
     */
    public function verify()
    {
        foreach ($this->_countValidators as $validator) {
            $validator->validate($this->_actualCount);
        }
    }
    
    /**
     * Check if passed arguments match an argument expectation
     *
     * @param array $args
     * @return bool
     */
    public function matchArgs(array $args)
    {
        if(empty($this->_expectedArgs)) {
            return true;
        }
        if(count($args) !== count($this->_expectedArgs)) {
            return false;
        }
        foreach ($args as $i=>$v) {
            if (!$this->_matchArg($this->_expectedArgs[$i], $v)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if passed argument matches an argument expectation
     *
     * @param array $args
     * @return bool
     */
    protected function _matchArg($expected, $actual)
    {
        if ($expected === $actual) {
            return true;
        }
        if ($expected == $actual) {
            return true;
        }
        if (is_string($expected) && !is_array($actual)) {
            $result = @preg_match($expected, (string) $actual);
            if($result === true) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Expected argument setter for the expectation
     *
     * @param mixed
     * @return self
     */
    public function with()
    {
        $this->_expectedArgs = func_get_args();
        return $this;
    }
    
    /**
     * Set with() as no arguments expected
     *
     * @return self
     */
    public function withNoArgs()
    {
        return $this->with();
    }
    
    /**
     * Set expectation that any arguments are acceptable
     *
     * @return self
     */
    public function withAnyArgs()
    {
        $this->_expectedArgs = array();
        return $this;
    }
    
    /**
     * Set a return value, or sequential queue of return values
     *
     * @return self
     */
    public function andReturn()
    {
        $this->_returnQueue = func_get_args();
        return $this;
    }
    
    /**
     * Return a self-returning black hole object.
     *
     * @return self
     */
    public function andReturnUndefined()
    {
        $this->andReturn(new \Mockery\Undefined);
        return $this;
    }
    
    /**
     * Set Exception class and arguments to that class to be thrown
     *
     * @param string $exception
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @return self
     */
    public function andThrow($exception, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->_throw = true;
        $this->andReturn(new $exception($message, $code, $previous));
        return $this;
    }
    
    /**
     * Indicates this expectation should occur zero or more times
     *
     * @return self
     */
    public function zeroOrMoreTimes()
    {
        $this->atLeast()->never();
    }
    
    /**
     * Indicates the number of times this expectation should occur
     *
     * @param int $limit
     */
    public function times($limit)
    {
        $this->_countValidators[] = new $this->_countValidatorClass($this, $limit);
        $this->_countValidatorClass = 'Mockery\CountValidator\Exact';
        return $this;
    }
    
    /**
     * Indicates that this expectation is never expected to be called
     *
     * @return self
     */
    public function never()
    {
        return $this->times(0);
    }
    
    /**
     * Indicates that this expectation is expected exactly once
     *
     * @return self
     */
    public function once()
    {
        return $this->times(1);
    }
    
    /**
     * Indicates that this expectation is expected exactly twice
     *
     * @return self
     */
    public function twice()
    {
        return $this->times(2);
    }
    
    /**
     * Sets next count validator to the AtLeast instance
     *
     * @return self
     */
    public function atLeast()
    {
        $this->_countValidatorClass = 'Mockery\CountValidator\AtLeast';
        return $this;
    }
    
    /**
     * Sets next count validator to the AtMost instance
     *
     * @return self
     */
    public function atMost()
    {
        $this->_countValidatorClass = 'Mockery\CountValidator\AtMost';
        return $this;
    }
    
    /**
     * Indicates that this expectation must be called in a specific given order
     *
     * @param string $group Name of the ordered group
     * @return self
     */
    public function ordered($group = null)
    {
        if ($this->_globally) {
            $this->_globalOrderNumber = $this->_defineOrdered($group, $this->_mock->mockery_getContainer());
        } else {
            $this->_orderNumber = $this->_defineOrdered($group, $this->_mock);
        }
        $this->_globally = false;
        return $this;
    }
    
    /**
     * Indicates call order should apply globally
     *
     * @return self
     */
    public function globally()
    {
        $this->_globally = true;
        return $this;
    }
    
    /**
     * Setup the ordering tracking on the mock or mock container
     *
     * @param string $group
     * @param object $ordering
     * @return int
     */
    protected function _defineOrdered($group, $ordering)
    {
        $groups = $ordering->mockery_getGroups();
        if (is_null($group)) {
            $result = $ordering->mockery_allocateOrder();
        } elseif (isset($groups[$group])) {
            $result = $groups[$group];
        } else {
            $result = $ordering->mockery_allocateOrder();
            $ordering->mockery_setGroup($group, $result);
        }
        return $result;
    }

}
