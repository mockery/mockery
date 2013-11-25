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

    protected $_mockery_ignoreMissingAsUndefined = false;
    
    /**
     * Flag to indicate whether we can defer method calls missing from our
     * expectations
     *
     * @var bool
     */
    protected $_mockery_deferMissing = false;

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
     * Instance of a core object on which methods are called in the event
     * it has been set, and an expectation for one of the object's methods
     * does not exist. This implements a simple partial mock proxy system.
     *
     * @var object
     */
    protected $_mockery_partial = null;
    
    /**
     * Flag to indicate we should ignore all expectations temporarily. Used
     * mainly to prevent expectation matching when in the middle of a mock
     * object recording session.
     *
     * @var bool
     */
    protected $_mockery_disableExpectationMatching = false;
    
    /**
     * Stores all stubbed public methods separate from any on-object public
     * properties that may exist.
     *
     * @var array
     */
    protected $_mockery_mockableProperties = array();
    
    /**
     * We want to avoid constructors since class is copied to Generator.php
     * for inclusion on extending class definitions.
     *
     * @param \Mockery\Container $container
     * @param object $partialObject
     * @return void
     */
    public function mockery_init(\Mockery\Container $container = null, $partialObject = null)
    {
        if(is_null($container)) {
            $container = new \Mockery\Container;
        }
        $this->_mockery_container = $container;
        if (!is_null($partialObject)) {
            $this->_mockery_partial = $partialObject;
        }
    }
    
    /**
     * Set expected method calls
     *
     * @param mixed
     * @return \Mockery\Expectation
     */
    public function shouldReceive()
    {
        $self = $this;
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
     * @return Mock
     */
    public function shouldIgnoreMissing()
    {
        $this->_mockery_ignoreMissing = true;
        return $this;
    }

    public function asUndefined()
    {
        $this->_mockery_ignoreMissingAsUndefined = true;
        return $this;
    }
    
    /**
     * Set mock to defer unexpected methods to it's parent
     *
     * This is particularly useless for this class, as it doesn't have a parent, 
     * but included for completeness
     *
     * @return Mock
     */
    public function shouldDeferMissing()
    {
        $this->_mockery_deferMissing = true;
        return $this;
    }

    /**
     * Create an obviously worded alias to shouldDeferMissing()
     *
     * @return Mock
     */
    public function makePartial()
    {
        return $this->shouldDeferMissing();
    }

    /**
     * Accepts a closure which is executed with an object recorder which proxies
     * to the partial source object. The intent being to record the
     * interactions of a concrete object as a set of expectations on the
     * current mock object. The partial may then be passed to a second process
     * to see if it fulfils the same (or exact same) contract as the original.
     *
     * @param Closure $closure
     */
    public function shouldExpect(\Closure $closure)
    {
        $recorder = new \Mockery\Recorder($this, $this->_mockery_partial);
        $this->_mockery_disableExpectationMatching = true;
        $closure($recorder);
        $this->_mockery_disableExpectationMatching = false;
        return $this;
    }
    
    /**
     * In the event shouldReceive() accepting one or more methods/returns,
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
        if (isset($this->_mockery_expectations[$method])
        && !$this->_mockery_disableExpectationMatching) {
            $handler = $this->_mockery_expectations[$method];
            
            try {
                return $handler->call($args);
            } catch (\Mockery\Exception\NoMatchingExpectationException $e) {
                if (!$this->_mockery_ignoreMissing && !$this->_mockery_deferMissing) {
                    throw $e;
                }
            }
        }
        
        if (!is_null($this->_mockery_partial) && method_exists($this->_mockery_partial, $method)) {
            return call_user_func_array(array($this->_mockery_partial, $method), $args);
        } elseif ($this->_mockery_deferMissing && is_callable("parent::$method")) {
            return call_user_func_array("parent::$method", $args);
        } elseif ($this->_mockery_ignoreMissing) {
            if ($this->_mockery_ignoreMissingAsUndefined === true) {
                $undef = new \Mockery\Undefined;
                return call_user_func_array(array($undef, $method), $args);
            } else {
                return null;
            }
        }
        throw new \BadMethodCallException(
            'Method ' . $this->_mockery_name . '::' . $method . '() does not exist on this mock object'
        );
    }
    
    /**
     * Forward calls to this magic method to the __call method
     */
    public function __toString()
    {
        return $this->__call('__toString', array());
    }
    
    /**public function __set($name, $value)
    {
        $this->_mockery_mockableProperties[$name] = $value;
        return $this;
    }
            	            
    public function __get($name)
    {
        if (isset($this->_mockery_mockableProperties[$name])) {
            return $this->_mockery_mockableProperties[$name];
        } elseif(isset($this->{$name})) {
            return $this->{$name};
        }   	                			    
        throw new \InvalidArgumentException (
            'Property ' . $this->_mockery_name . '::' . $name . ' does not exist on this mock object'
        );
    }**/
    
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
            $exception = new \Mockery\Exception\InvalidOrderException(
                'Method ' . $this->_mockery_name . '::' . $method . '()'
                . ' called out of order: expected order '
                . $order . ', was ' . $this->_mockery_currentOrder
            );
            $exception->setMock($this)
                ->setMethodName($method)
                ->setExpectedOrder($order)
                ->setActualOrder($this->_mockery_currentOrder);
            throw $exception;
        }
        $this->mockery_setCurrentOrder($order);
    }
    
    /**
     * Gets the count of expectations for this mock
     *
     * @return int
     */
    public function mockery_getExpectationCount()
    {
        $count = 0;
        foreach($this->_mockery_expectations as $director) {
            $count += $director->getExpectationCount();
        }
        return $count;
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
    
    public function mockery_getMockableProperties()
    {
        return $this->_mockery_mockableProperties;
    }

    /**
     * Calls a parent class method and returns the result. Used in a passthru
     * expectation where a real return value is required while still taking
     * advantage of expectation matching and call count verification.
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function mockery_callSubjectMethod($name, array $args)
    {
        return call_user_func_array('parent::' . $name, $args);
    }

}
