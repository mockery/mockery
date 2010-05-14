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

class Mock
{

    /**
     * Stores an array of all expectation directors for this mock
     *
     * @var array
     */
    protected $_expectations = array();
    
    /**
     * Last expectation that was set
     *
     * @var object
     */
    protected $_lastExpectation = null;
    
    /**
     * Flag to indicate whether we can ignore method calls missing from our
     * expectations
     *
     * @var bool
     */
    protected $_ignoreMissing = false;
    
    /**
     * Flag to indicate whether this mock was verified
     *
     * @var bool
     */
    protected $_verified = false;
    
    /**
     * Given name of the mock
     *
     * @var string
     */
    protected $_name = null;
    
    /**
     * Order number of allocation
     *
     * @var int
     */
    protected $_allocatedOrder = 0;
    
    /**
     * Current ordered number
     *
     * @var int
     */
    protected $_currentOrder = 0;
    
    /**
     * Ordered groups
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Set expected method calls
     *
     * @param string $method
     * @return
     */
    public function shouldReceive($method)
    {
        if (!isset($this->_expectations[$method])) {
            $this->_expectations[$method] = new \Mockery\ExpectationDirector($method);
        }
        $expectation = new \Mockery\Expectation($this, $method);
        $this->_expectations[$method]->addExpectation($expectation);
        $this->_lastExpectation = $expectation;
        return $expectation;
    }
    
    /**
     * Capture calls to this mock
     */
    public function __call($method, array $args) {
        if (isset($this->_expectations[$method])) {
            $handler = $this->_expectations[$method];
            return $handler->call($args);
        } elseif ($this->_ignoreMissing) {
            $return = new \Mockery\Undefined;
            return $return;
        }
    }
    
    /**
     * Iterate across all expectation directors and validate each
     *
     * @throws \Mockery\CountValidator\Exception
     * @return void
     */
    public function mockery_verify()
    {
        if ($this->_verified) return true;
        $this->_verified = true;
        foreach($this->_expectations as $director) {
            $director->verify();
        }
    }
    
    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder()
    {
        $this->_allocatedOrder += 1;
        return $this->_allocatedOrder;
    }
    
    /**
     * Fetch array of ordered groups
     *
     * @return array
     */
    public function mockery_getGroups()
    {
        return $this->_groups;
    }
    
    /**
     * Set current ordered number
     *
     * @param int $order
     */
    public function mockery_setCurrentOrder($order)
    {
        $this->_currentOrder = $order;
        return $this->_currentOrder;
    }
    
    /**
     * Get current ordered number
     *
     * @return int
     */
    public function mockery_getCurrentOrder()
    {
        return $this->_currentOrder;
    }
    
    /**
     * Validate the current mock's ordering
     *
     * @param string $method
     * @param int $order
     * @throws \Mockery\Exception
     * @return void
     */
    public function mockery_validateOrder($method, $order)
    {
        if ($order < $this->_currentOrder) {
            throw new \Mockery\Exception(
                'Method ' . $method . ' called out of order: expected order '
                . $order . ', was ' . $this->_currentOrder
            );
        }
        $this->mockery_setCurrentOrder($order);
    }

}
