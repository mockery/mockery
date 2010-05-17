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

class Mock implements MockInterface
{

    /**
     * Stores an array of all expectation directors for this mock
     *
     * @var array
     */
    protected $_mockery_expectations = array();
    
    /**
     * Last expectation that was set
     *
     * @var object
     */
    protected $_mockery_lastExpectation = null;
    
    /**
     * Flag to indicate whether we can ignore method calls missing from our
     * expectations
     *
     * @var bool
     */
    protected $_mockery_ignoreMissing = false;
    
    /**
     * Flag to indicate whether this mock was verified
     *
     * @var bool
     */
    protected $_mockery_verified = false;
    
    /**
     * Given name of the mock
     *
     * @var string
     */
    protected $_mockery_name = null;
    
    /**
     * Order number of allocation
     *
     * @var int
     */
    protected $_mockery_allocatedOrder = 0;
    
    /**
     * Current ordered number
     *
     * @var int
     */
    protected $_mockery_currentOrder = 0;
    
    /**
     * Ordered groups
     *
     * @var array
     */
    protected $_mockery_groups = array();
    
    /**
     * Mock container containing this mock object
     *
     * @var \Mockery\Container
     */
    protected $_mockery_container = null;

    /**
     * Constructor
     *
     * @param string $name
     * @param \Mockery\Container $container
     */
    public function __construct($name, \Mockery\Container $container = null)
    {
        $this->_mockery_name = $name;
        if(is_null($container)) {
            $container = new \Mockery\Container;
        }
        $this->_mockery_container = $container;
    }
    
    /**
     * Alternative setup method to constructor
     *
     * @param string $name
     * @param \Mockery\Container $container
     * @return void
     */
    public function mockery_init($name, \Mockery\Container $container = null)
    {
        $this->_mockery_name = $name;
        if(is_null($container)) {
            $container = new \Mockery\Container;
        }
        $this->_mockery_container = $container;
    }
    
    /**
     * Set expected method calls
     *
     * @param mixed
     * @return \Mockery\Expectation
     */
    public function shouldReceive()
    {
        $self =& $this;
        $lastExpectation = \Mockery::parseShouldReturnArgs(
            $this, func_get_args(), function($method) use ($self) {
                $director = $self->mockery_getExpectationsFor($method);
                if (!$director) {
                    $director = new \Mockery\ExpectationDirector($method, $self);
                    $self->mockery_setExpectationsFor($method, $director);
                }
                $expectation = new \Mockery\Expectation($self, $method);
                $director->addExpectation($expectation);
                return $expectation;
            }
        );
        return $lastExpectation;
    }
    
    /**
     * Set mock to ignore unexpected methods and return Undefined class
     *
     * @return void
     */
    public function shouldIgnoreMissing()
    {
        $this->_mockery_ignoreMissing = true;
    }
    
    /**
     * In the event shouldReceive() accepting an array of methods/returns
     * this method will switch them from normal expectations to default
     * expectations
     *
     * @return self
     */
    public function byDefault()
    {
        foreach ($this->_mockery_expectations as $director) {
            $exps = $director->getExpectations();
            foreach ($exps as $exp) {
                $exp->byDefault();
            }
        }
        return $this;
    }
    
    /**
     * Capture calls to this mock
     */
    public function __call($method, array $args)
    {
        if (isset($this->_mockery_expectations[$method])) {
            $handler = $this->_mockery_expectations[$method];
            return $handler->call($args);
        } elseif ($this->_mockery_ignoreMissing) {
            $return = new \Mockery\Undefined;
            return $return;
        }
        throw new \BadMethodCallException(
            'Method ' . $this->_mockery_name . '::' . $method . ' does not exist on this mock object'
        );
    }
    
    /**
     * Iterate across all expectation directors and validate each
     *
     * @throws \Mockery\CountValidator\Exception
     * @return void
     */
    public function mockery_verify()
    {
        if ($this->_mockery_verified) return true;
        $this->_mockery_verified = true;
        foreach($this->_mockery_expectations as $director) {
            $director->verify();
        }
    }
    
    /**
     * Tear down tasks for this mock
     *
     * @return void
     */
    public function mockery_teardown()
    {
        
    }
    
    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder()
    {
        $this->_mockery_allocatedOrder += 1;
        return $this->_mockery_allocatedOrder;
    }
    
    /**
     * Set ordering for a group
     *
     * @param mixed $group
     * @param int $order
     */
    public function mockery_setGroup($group, $order)
    {
        $this->_mockery_groups[$group] = $order;
    }
    
    /**
     * Fetch array of ordered groups
     *
     * @return array
     */
    public function mockery_getGroups()
    {
        return $this->_mockery_groups;
    }
    
    /**
     * Set current ordered number
     *
     * @param int $order
     */
    public function mockery_setCurrentOrder($order)
    {
        $this->_mockery_currentOrder = $order;
        return $this->_mockery_currentOrder;
    }
    
    /**
     * Get current ordered number
     *
     * @return int
     */
    public function mockery_getCurrentOrder()
    {
        return $this->_mockery_currentOrder;
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
        if ($order < $this->_mockery_currentOrder) {
            throw new \Mockery\Exception(
                'Method ' . $method . ' called out of order: expected order '
                . $order . ', was ' . $this->_mockery_currentOrder
            );
        }
        $this->mockery_setCurrentOrder($order);
    }
    
    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return \Mockery\ExpectationDirector|null
     */
    public function mockery_setExpectationsFor($method, \Mockery\ExpectationDirector $director)
    {
        $this->_mockery_expectations[$method] = $director;
    }
    
    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return \Mockery\ExpectationDirector|null
     */
    public function mockery_getExpectationsFor($method)
    {
        if (isset($this->_mockery_expectations[$method])) {
            return $this->_mockery_expectations[$method];
        }
    }
    
    /**
     * Find an expectation matching the given method and arguments
     *
     * @var string $method
     * @var array $args
     * @return \Mockery\Expectation|null
     */
    public function mockery_findExpectation($method, array $args)
    {
        if (!isset($this->_mockery_expectations[$method])) {
            return null;
        }
        $director = $this->_mockery_expectations[$method];
        return $director->findExpectation($args);
    }
    
    /**
     * Return the container for this mock
     *
     * @return \Mockery\Container
     */
    public function mockery_getContainer()
    {
        return $this->_mockery_container;
    }
    
    /**
     * Return the name for this mock
     *
     * @return string
     */
    public function mockery_getName()
    {
        return $this->_mockery_name;
    }

}
