<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class CompositeExpectation implements ExpectationInterface
{
    /**
     * Stores an array of all expectations for this composite
     *
     * @var array
     */
    protected $_expectations = array();

    /**
     * Add an expectation to the composite
     *
     * @param \Mockery\Expectation|\Mockery\CompositeExpectation $expectation
     * @return void
     */
    public function add($expectation)
    {
        $this->_expectations[] = $expectation;
    }

    /**
     * @param mixed ...$args
     */
    public function andReturn(...$args)
    {
        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * Set a return value, or sequential queue of return values
     *
     * @param mixed ...$args
     * @return self
     */
    public function andReturns(...$args)
    {
        return call_user_func_array([$this, 'andReturn'], $args);
    }

    /**
     * Intercept any expectation calls and direct against all expectations
     *
     * @param string $method
     * @param array $args
     * @return self
     */
    public function __call($method, array $args)
    {
        foreach ($this->_expectations as $expectation) {
            call_user_func_array(array($expectation, $method), $args);
        }
        return $this;
    }

    /**
     * Return order number of the first expectation
     *
     * @return int
     */
    public function getOrderNumber()
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getOrderNumber();
    }

    /**
     * Return the parent mock of the first expectation
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    public function getMock()
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getMock();
    }

    /**
     * Mockery API alias to getMock
     *
     * @return \Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    public function mock()
    {
        return $this->getMock();
    }

    /**
     * Starts a new expectation addition on the first mock which is the primary
     * target outside of a demeter chain
     *
     * @param mixed ...$args
     * @return \Mockery\Expectation
     */
    public function shouldReceive(...$args)
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return call_user_func_array(array($first->getMock(), 'shouldReceive'), $args);
    }

    /**
     * Starts a new expectation addition on the first mock which is the primary
     * target outside of a demeter chain
     *
     * @param mixed ...$args
     * @return \Mockery\Expectation
     */
    public function shouldNotReceive(...$args)
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return call_user_func_array(array($first->getMock(), 'shouldNotReceive'), $args);
    }

    /**
     * Return the string summary of this composite expectation
     *
     * @return string
     */
    public function __toString()
    {
        $return = '[';
        $parts = array();
        foreach ($this->_expectations as $exp) {
            $parts[] = (string) $exp;
        }
        $return .= implode(', ', $parts) . ']';
        return $return;
    }
}
