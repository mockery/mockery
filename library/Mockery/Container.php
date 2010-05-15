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

class Container
{

    /**
     * Store of mock objects
     *
     * @var array
     */
    protected $_mocks = array();
    
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
     * Generates a new mock object for this container
     *
     * @return \Mockery\Mock
     */
    public function mock()
    {
        $class = null;
        $name = null;
        $quickdefs = array();
        $args = func_get_args();
        while (count($args) > 0) {
            $arg = current($args);
            if (is_string($arg) && class_exists($arg)) {
                $class = array_shift($args);
            } elseif (is_string($arg)) {
                $name = array_shift($args);
            } elseif (is_array($arg)) {
                $quickdefs = array_shift($args);
            } else {
                throw new \Mockery\Exception(
                    'Unable to parse arguments sent to mock()'
                );
            }
        }
        
        if (!is_null($name)) {
            $mock = new \Mockery\Mock($name, $this);
        }
        if (!empty($quickdefs)) {
            $mock->shouldReceive($quickdefs);
        }
        $this->rememberMock($mock);
        return $mock;
    }
    
    /**
     *  Tear down tasks for this container
     *
     * @return void
     */
    public function mockery_teardown()
    {
        try {
            $this->mockery_verify();
        } catch (\Exception $e) {
            $this->mockery_close();
            throw $e;
        }
    }
    
    /**
     * Verify the container mocks
     *
     * @return void
     */
    public function mockery_verify()
    {
        foreach($this->_mocks as $mock) {
            $mock->mockery_verify();
        }
    }
    
    /**
     * Reset the container to its original state
     *
     * @return void
     */
    public function mockery_close()
    {
        foreach($this->_mocks as $mock) {
            $mock->mockery_teardown();
        }
        $this->_mocks = array();
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
     * Set ordering for a group
     *
     * @param mixed $group
     * @param int $order
     */
    public function mockery_setGroup($group, $order)
    {
        $this->_groups[$group] = $order;
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
    
    /**
     * Store a mock and set its container reference
     *
     * @param \Mockery\Mock
     * @return \Mockery\Mock
     */
    public function rememberMock(\Mockery\Mock $mock)
    {
        $this->_mocks[] = $mock;
        return $mock;
    }

}
