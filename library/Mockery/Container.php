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
    const BLOCKS = \Mockery::BLOCKS;

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
     * I apologies in advance for this. A God Method just fits the API which
     * doesn't require differentiating between classes, interfaces, abstracts,
     * names or partials - just so long as it's something that can be mocked.
     * I'll refactor it one day so it's easier to follow.
     *
     * @return \Mockery\Mock
     */
    public function mock()
    {
        $class = null;
        $name = null;
        $partial = null;
        $expectationClosure = null;
        $quickdefs = array();
        $constructorArgs = null;
        $blocks = array();
        $makeInstanceMock = false;
        $args = func_get_args();
        $partialMethods = array();
        if (count($args) > 1) {
            $finalArg = end($args);
            reset($args);
            if (is_callable($finalArg) && !is_array($finalArg)) {
                $expectationClosure = array_pop($args);
            }
        }
        while (count($args) > 0) {
            $arg = current($args);
            // check for multiple interfaces
            if (is_string($arg) && strpos($arg, ',') && !strpos($arg, ']')) {
                $interfaces = explode(',', str_replace(' ', '', $arg));
                foreach ($interfaces as $i) {
                    if (!interface_exists($i, true) && !class_exists($i, true)) {
                        throw new \Mockery\Exception(
                            'Class name follows the format for defining multiple'
                            . ' interfaces, however one or more of the interfaces'
                            . ' do not exist or are not included, or the base class'
                            . ' (which you may omit from the mock definition) does not exist'
                        );
                    }
                }
                $class = $interfaces;
                array_shift($args);
            } elseif (is_string($arg) && substr($arg, 0, 6) == 'alias:') {
                $class = 'stdClass';
                $name = array_shift($args);
                $name = str_replace('alias:', '', $name);
            } elseif (is_string($arg) && substr($arg, 0, 9) == 'overload:') {
                $class = 'stdClass';
                $name = array_shift($args);
                $name = str_replace('overload:', '', $name);
                $makeInstanceMock = true;
            } elseif (is_string($arg) && substr($arg, strlen($arg)-1, 1) == ']') {
                $parts = explode('[', $arg);
                if (!class_exists($parts[0], true) && !interface_exists($parts[0], true)) {
                    throw new \Mockery\Exception('Can only create a partial mock from'
                    . ' an existing class or interface');
                }
                $class = $parts[0];
                $parts[1] = str_replace(' ','', $parts[1]);
                $partialMethods = explode(',', strtolower(rtrim($parts[1], ']')));
                array_shift($args);
            } elseif (is_string($arg) && (class_exists($arg, true) || interface_exists($arg, true))) {
                $class = array_shift($args);
            } elseif (is_string($arg)) {
                $class = array_shift($args);
                $this->declareClass($class);
            } elseif (is_object($arg)) {
                $partial = array_shift($args);
            } elseif (is_array($arg) && !empty($arg) && array_keys($arg) !== range(0, count($arg) - 1)) {
                // if associative array
                if(array_key_exists(self::BLOCKS, $arg)) $blocks = $arg[self::BLOCKS]; unset($arg[self::BLOCKS]);
                $quickdefs = array_shift($args);
            } elseif (is_array($arg)) {
                $constructorArgs = array_shift($args);
                $blocks[] = "__construct"; // Assume they want to use the actual constructor regardless of other requirements
            } else {
                throw new \Mockery\Exception(
                    'Unable to parse arguments sent to '
                    . get_class($this) . '::mock()'
                );
            }
        }

        if (!empty($partialMethods) && $constructorArgs === null) {
            $constructorArgs = array();
        }

        if (!is_null($name) && !is_null($class)) {
            if (!$makeInstanceMock) {
                $mockName = \Mockery\Generator::createClassMock($class, null, null, $blocks);
            } else {
                $mockName = \Mockery\Generator::createClassMock($class, null, null, $blocks, true);
            }
            $result = class_alias($mockName, $name);
            $mock = $this->_getInstance($name, $constructorArgs);
            $mock->mockery_init($class, $this);
        } elseif (!is_null($name)) {
            $mock = new \Mockery\Mock();
            $mock->mockery_init($name, $this);
        } elseif(!is_null($class)) {
            $mockName = \Mockery\Generator::createClassMock($class, null, null, $blocks, false, $partialMethods);
            $mock = $this->_getInstance($mockName, $constructorArgs);
            $mock->mockery_init($class, $this);
        } elseif(!is_null($partial)) {
            $mockName = \Mockery\Generator::createClassMock(get_class($partial), null, true, $blocks);
            $mock = $this->_getInstance($mockName, $constructorArgs);
            $mock->mockery_init(get_class($partial), $this, $partial);
        } else {
            $mock = new \Mockery\Mock();
            $mock->mockery_init('unknown', $this);
        }
        if (!empty($quickdefs)) {
            $mock->shouldReceive($quickdefs);
        }
        if (!empty($expectationClosure)) {
            $expectationClosure($mock);
        }
        $this->rememberMock($mock);
        return $mock;
    }
    
    public function instanceMock()
    {
        
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
     * @return int The current order number that was set
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
    public function mockery_validateOrder($method, $order, \Mockery\MockInterface $mock)
    {
        if ($order < $this->_currentOrder) {
            $exception = new \Mockery\Exception\InvalidOrderException(
                'Method ' . $method . ' called out of order: expected order '
                . $order . ', was ' . $this->_currentOrder
            );
            $exception->setMock($mock)
                ->setMethodName($method)
                ->setExpectedOrder($order)
                ->setActualOrder($this->_currentOrder);
            throw $exception;
        }
        $this->mockery_setCurrentOrder($order);
    }
    
    /**
     * Gets the count of expectations on the mocks
     *
     * @return int
     */
    public function mockery_getExpectationCount()
    {
        $count = 0;
        foreach($this->_mocks as $mock) {
            $count += $mock->mockery_getExpectationCount();
        }
        return $count;
    }
    
    /**
     * Store a mock and set its container reference
     *
     * @param \Mockery\Mock
     * @return \Mockery\Mock
     */
    public function rememberMock(\Mockery\MockInterface $mock)
    {
        if (!isset($this->_mocks[get_class($mock)])) {
            $this->_mocks[get_class($mock)] = $mock;
        } else {
            /**
             * This condition triggers for an instance mock where origin mock
             * is already remembered
             */
            $this->_mocks[] = $mock;
        }
        return $mock;
    }

    /**
     * Retrieve the last remembered mock object, which is the same as saying
     * retrieve the current mock being programmed where you have yet to call
     * mock() to change it - thus why the method name is "self" since it will be
     * be used during the programming of the same mock.
     *
     * @return \Mockery\Mock
     */
    public function self()
    {
        $mocks = array_values($this->_mocks);
        $index = count($mocks) - 1;
        return $mocks[$index];
    }
    
    /**
     * Return a specific remembered mock according to the array index it
     * was stored to in this container instance
     *
     * @return \Mockery\Mock
     */
    public function fetchMock($reference)
    {
        if (isset($this->_mocks[$reference])) return $this->_mocks[$reference];
    }
    
    protected function _getInstance($mockName, $constructorArgs = null)
    {
        $r = new \ReflectionClass($mockName);
        
        if (null === $r->getConstructor()) {
            $return = new $mockName;
            return $return;
        }

        if ($constructorArgs !== null) {
            return $r->newInstanceArgs($constructorArgs);
        }

        $return = unserialize(sprintf('O:%d:"%s":0:{}', strlen($mockName), $mockName));
        return $return;
    }

    /**
     * Takes a class name and declares it
     *
     * @param string $fqcn
     */
    public function declareClass($fqcn)
    {
        if (false !== strpos($fqcn, '/')) {
            throw new \Mockery\Exception(
                'Class name contains a forward slash instead of backslash needed '
                . 'when employing namespaces'
            );
        }
        if (false !== strpos($fqcn, "\\")) {
            $parts = array_filter(explode("\\", $fqcn), function($part) {
                return $part !== "";
            });
            $cl = array_pop($parts);
            $ns = implode("\\", $parts);
            eval(" namespace $ns { class $cl {} }");
        } else {
            eval(" class $fqcn {} ");
        }
    }

}
