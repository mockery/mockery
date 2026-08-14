<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Mockery\Container;
use Mockery\CountValidator\Exception;
use Mockery\Exception\BadMethodCallException;
use Mockery\Exception\InvalidOrderException;
use Mockery\Exception\NoMatchingExpectationException;
use Mockery\Expectation;
use Mockery\ExpectationDirector;
use Mockery\ExpectsHigherOrderMessage;
use Mockery\HigherOrderMessage;
use Mockery\LegacyMockInterface;
use Mockery\MethodCall;
use Mockery\MockInterface;
use Mockery\ReceivedMethodCalls;
use Mockery\Reflector;
use Mockery\Undefined;
use Mockery\VerificationDirector;
use Mockery\VerificationExpectation;

#[\AllowDynamicProperties]
class Mock implements MockInterface
{
    /**
     * Stores an array of all expectation directors for this mock
     *
     * @var array<string,ExpectationDirector>
     */
    protected $_mockery_expectations = [];

    /**
     * Stores an initial number of expectations that can be manipulated
     * while using the getter method.
     *
     * @var int
     */
    protected $_mockery_expectations_count = 0;

    /**
     * Flag to indicate whether we can ignore method calls missing from our
     * expectations
     *
     * @var bool
     */
    protected $_mockery_ignoreMissing = false;

    /**
     * Flag to indicate whether we want to set the ignoreMissing flag on
     * mocks generated form this calls to this one
     *
     * @var bool
     */
    protected $_mockery_ignoreMissingRecursive = false;

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
     * @var array<string,int>
     */
    protected $_mockery_groups = [];

    /**
     * Mock container containing this mock object
     *
     * @var Container
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
     * @var array<string,mixed>
     */
    protected $_mockery_mockableProperties = [];

    /**
     * @var list<string>
     */
    protected $_mockery_mockableMethods = [];

    /**
     * Just a local cache for this mock's target's methods
     *
     * @var \ReflectionMethod[]
     */
    protected static $_mockery_methods;

    protected $_mockery_allowMockingProtectedMethods = false;

    protected $_mockery_receivedMethodCalls;

    /**
     * If shouldIgnoreMissing is called, this value will be returned on all calls to missing methods
     * @var mixed
     */
    protected $_mockery_defaultReturnValue = null;

    /**
     * Tracks internally all the bad method call exceptions that happened during runtime
     *
     * @var array<BadMethodCallException>
     */
    protected $_mockery_thrownExceptions = [];

    protected $_mockery_instanceMock = true;

    /** @var null|class-string $parentClass */
    private $_mockery_parentClass = null;

    /**
     * We want to avoid constructors since class is copied to Generator.php
     * for inclusion on extending class definitions.
     *
     * @param Container $container
     * @param object|null $partialObject
     * @param bool $instanceMock
     * @return void
     */
    #[\Override]
    public function mockery_init(?Container $container = null, $partialObject = null, $instanceMock = true)
    {
        if (! $container instanceof Container) {
            $container = new Container();
        }

        $this->_mockery_container = $container;

        if (is_object($partialObject)) {
            $this->_mockery_partial = $partialObject;
        }

        if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
            foreach ($this->mockery_getMethods() as $method) {
                if ($method->isPublic()) {
                    $this->_mockery_mockableMethods[] = $method->getName();
                }
            }
        }

        $this->_mockery_instanceMock = $instanceMock;

        $this->_mockery_parentClass = get_parent_class($this);
    }

    /**
     * Set expected method calls
     *
     * @param string|array<string,mixed> ...$methodNames one or many methods that are expected to be called in this mock
     *
     * @return ($methodNames is list{} ? HigherOrderMessage : Expectation)
     */
    #[\Override]
    public function shouldReceive(...$methodNames)
    {
        if ($methodNames === []) {
            return new HigherOrderMessage($this, 'shouldReceive');
        }

        foreach ($methodNames as $method) {
            if ('' === $method) {
                throw new \InvalidArgumentException('Received empty method name');
            }
        }

        $self = $this;
        $allowMockingProtectedMethods = $this->_mockery_allowMockingProtectedMethods;

        /** @var Expectation&ExpectationInterface $expectation */
        $expectation = \Mockery::parseShouldReturnArgs(
            $this,
            $methodNames,
            static function ($method) use ($self, $allowMockingProtectedMethods) {
                $reflectionMethod = $self->mockery_getMethod($method);
                if ($reflectionMethod) {
                    if ($reflectionMethod->isPrivate()) {
                        throw new \InvalidArgumentException($method . '() cannot be mocked as it is a private method');
                    }

                    if (!$allowMockingProtectedMethods && $reflectionMethod->isProtected()) {
                        throw new \InvalidArgumentException($method . '() cannot be mocked as it is a protected method and mocking protected methods is not enabled for the currently used mock object. Use shouldAllowMockingProtectedMethods() to enable mocking of protected methods.');
                    }
                }

                $director = $self->mockery_getExpectationsFor($method);
                if (!$director instanceof ExpectationDirector) {
                    $director = new ExpectationDirector($method, $self);
                    $self->mockery_setExpectationsFor($method, $director);
                }

                $expectation = new Expectation($self, $method);
                $director->addExpectation($expectation);
                return $expectation;
            }
        );
        return $expectation;
    }

    // start method allows
    /**
     * @param mixed $something  String method name or map of method => return
     *
     * @return ($something is string ? Expectation : ($something is list{} ? HigherOrderMessage : static))
     */
    public function allows($something = [])
    {
        if (is_string($something)) {
            return $this->shouldReceive($something);
        }

        if (empty($something)) {
            return $this->shouldReceive();
        }

        foreach ($something as $method => $returnValue) {
            $this->shouldReceive($method)->andReturn($returnValue);
        }

        return $this;
    }

    // end method allows
    // start method expects
    /**
    * @param mixed $something  String method name (optional)
    *
    * @return ($something is string ? Expectation : ExpectsHigherOrderMessage)
    */
    public function expects($something = null)
    {
        if (is_string($something)) {
            return $this->shouldReceive($something)->once();
        }

        return new ExpectsHigherOrderMessage($this);
    }

    // end method expects
    /**
     * Shortcut method for setting an expectation that a method should not be called.
     *
     * @param string|array<string,mixed> ...$methodNames one or many methods that are expected not to be called in this mock
     *
     * @return ($methodNames is list{} ? HigherOrderMessage : Expectation)
     */
    #[\Override]
    public function shouldNotReceive(...$methodNames)
    {
        if ($methodNames === []) {
            return new HigherOrderMessage($this, 'shouldNotReceive');
        }

        /** @var ?Expectation $expectation */
        $expectation = null;

        foreach ($methodNames as $methodName) {
            $expectation = $this->shouldReceive($methodName);
            $expectation->never();
        }

        return $expectation;
    }

    /**
     * Allows additional methods to be mocked that do not explicitly exist on mocked class
     *
     * @param string $method name of the method to be mocked
     *
     * @return static
     */
    #[\Override]
    public function shouldAllowMockingMethod($method)
    {
        $this->_mockery_mockableMethods[] = $method;
        return $this;
    }

    /**
     * Set mock to ignore unexpected methods and return Undefined class
     * @param mixed $returnValue the default return value for calls to missing functions on this mock
     * @param bool $recursive Specify if returned mocks should also have shouldIgnoreMissing set
     * @return static
     */
    #[\Override]
    public function shouldIgnoreMissing($returnValue = null, $recursive = false)
    {
        $this->_mockery_ignoreMissing = true;
        $this->_mockery_ignoreMissingRecursive = $recursive;
        $this->_mockery_defaultReturnValue = $returnValue;
        return $this;
    }

    /**
     * @return static
     */
    public function asUndefined()
    {
        $this->_mockery_ignoreMissing = true;
        $this->_mockery_defaultReturnValue = new Undefined();
        return $this;
    }

    /**
     * @return static
     */
    #[\Override]
    public function shouldAllowMockingProtectedMethods()
    {
        if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
            foreach ($this->mockery_getMethods() as $method) {
                if ($method->isProtected()) {
                    $this->_mockery_mockableMethods[] = $method->getName();
                }
            }
        }

        $this->_mockery_allowMockingProtectedMethods = true;
        return $this;
    }


    /**
     * Set mock to defer unexpected methods to it's parent
     *
     * This is particularly useless for this class, as it doesn't have a parent,
     * but included for completeness
     *
     * @deprecated 2.0.0 Please use makePartial() instead
     *
     * @return static
     */
    #[\Override]
    public function shouldDeferMissing()
    {
        return $this->makePartial();
    }

    /**
     * Set mock to defer unexpected methods to it's parent
     *
     * It was an alias for shouldDeferMissing(), which will be removed
     * in 2.0.0.
     *
     * @return static
     */
    #[\Override]
    public function makePartial()
    {
        $this->_mockery_deferMissing = true;
        return $this;
    }

    /**
     * In the event shouldReceive() accepting one or more methods/returns,
     * this method will switch them from normal expectations to default
     * expectations
     *
     * @return static
     */
    #[\Override]
    public function byDefault()
    {
        foreach ($this->_mockery_expectations as $mockeryExpectation) {
            $expectations = $mockeryExpectation->getExpectations();
            foreach ($expectations as $expectation) {
                $expectation->byDefault();
            }
        }

        return $this;
    }

    /**
     * Capture calls to this mock
     * 
     * @param string $method
     * @param array<mixed> $args
     * @return mixed
     * 
     * @throws \Throwable
     */
    public function __call($method, array $args)
    {
        return $this->_mockery_handleMethodCall($method, $args);
    }

    public static function __callStatic($method, array $args)
    {
        return self::_mockery_handleStaticMethodCall($method, $args);
    }

    /**
     * Forward calls to this magic method to the __call method
     * 
     * @throws \Throwable
     *
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function __toString()
    {
        return $this->__call('__toString', []);
    }

    /**
     * Iterate across all expectation directors and validate each
     *
     * @throws Exception
     * @return void
     */
    #[\Override]
    public function mockery_verify()
    {
        if ($this->_mockery_verified) {
            return;
        }

        if (
            property_exists($this, '_mockery_ignoreVerification') && 
            $this->_mockery_ignoreVerification === true
        ) {
            return;
        }

        $this->_mockery_verified = true;
        foreach ($this->_mockery_expectations as $mockeryExpectation) {
            $mockeryExpectation->verify();
        }
    }

    /**
     * Gets a list of exceptions thrown by this mock
     *
     * @return array<BadMethodCallException>
     */
    public function mockery_thrownExceptions()
    {
        return $this->_mockery_thrownExceptions;
    }

    /**
     * Tear down tasks for this mock
     *
     * @return void
     */
    #[\Override]
    public function mockery_teardown()
    {
    }

    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    #[\Override]
    public function mockery_allocateOrder()
    {
        return ++$this->_mockery_allocatedOrder;
    }

    /**
     * Set ordering for a group
     *
     * @param string $group
     * @param int $order
     */
    #[\Override]
    public function mockery_setGroup($group, $order)
    {
        $this->_mockery_groups[$group] = $order;
    }

    /**
     * Fetch array of ordered groups
     *
     * @return array<string,int>
     */
    #[\Override]
    public function mockery_getGroups()
    {
        return $this->_mockery_groups;
    }

    /**
     * Set current ordered number
     *
     * @param int $order
     * @return void
     */
    #[\Override]
    public function mockery_setCurrentOrder($order)
    {
        $this->_mockery_currentOrder = $order;
    }

    /**
     * Get current ordered number
     *
     * @return int
     */
    #[\Override]
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
    #[\Override]
    public function mockery_validateOrder($method, $order)
    {
        if ($order < $this->_mockery_currentOrder) {
            $invalidOrderException = new InvalidOrderException(sprintf(
                'Method %s::%s() called out of order: expected order %d, was %d',
                self::class,
                $method,
                $order,
                $this->_mockery_currentOrder
            ));
            $invalidOrderException->setMock($this)
                ->setMethodName($method)
                ->setExpectedOrder($order)
                ->setActualOrder($this->_mockery_currentOrder);
            throw $invalidOrderException;
        }

        $this->mockery_setCurrentOrder($order);
    }

    /**
     * Gets the count of expectations for this mock
     *
     * @return int
     */
    #[\Override]
    public function mockery_getExpectationCount()
    {
        $count = $this->_mockery_expectations_count;
        foreach ($this->_mockery_expectations as $mockeryExpectation) {
            $count += $mockeryExpectation->getExpectationCount();
        }

        return $count;
    }

    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return void
     */
    #[\Override]
    public function mockery_setExpectationsFor($method, ExpectationDirector $director)
    {
        $this->_mockery_expectations[$method] = $director;
    }

    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return ExpectationDirector|null
     */
    #[\Override]
    public function mockery_getExpectationsFor($method)
    {
        if (isset($this->_mockery_expectations[$method])) {
            return $this->_mockery_expectations[$method];
        }
        return null;
    }

    /**
     * Find an expectation matching the given method and arguments
     *
     * @var string $method
     * @var array $args
     * @return Expectation|null
     */
    #[\Override]
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
     * @return Container
     */
    #[\Override]
    public function mockery_getContainer()
    {
        return $this->_mockery_container;
    }

    /**
     * Return the name for this mock
     *
     * @return string
     */
    #[\Override]
    public function mockery_getName()
    {
        return self::class;
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    public function mockery_getMockableProperties()
    {
        return $this->_mockery_mockableProperties;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        if (false !== stripos($name, '_mockery_')) {
            return false;
        }

        if (!$this->_mockery_parentClass) {
            return false;
        }

        if (!method_exists($this->_mockery_parentClass, '__isset')) {
            return false;
        }

        return call_user_func($this->_mockery_parentClass . '::__isset', $name);
    }

    /**
     * @return array<string,ExpectationDirector>
     */
    public function mockery_getExpectations()
    {
        return $this->_mockery_expectations;
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
        if (!method_exists($this, $name) && $this->_mockery_parentClass && method_exists($this->_mockery_parentClass, '__call')) {
            return call_user_func($this->_mockery_parentClass . '::__call', $name, $args);
        }

        return call_user_func_array($this->_mockery_parentClass . '::' . $name, $args);
    }

    /**
     * @return list<string>
     */
    #[\Override]
    public function mockery_getMockableMethods()
    {
        return $this->_mockery_mockableMethods;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function mockery_isAnonymous()
    {
        $reflectionClass = new \ReflectionClass($this);

        // PHP 8 has Stringable interface
        $interfaces = array_filter($reflectionClass->getInterfaces(), static function ($i): bool {
            return $i->getName() !== 'Stringable';
        });

        return false === $reflectionClass->getParentClass() && 2 === count($interfaces);
    }

    public function mockery_isInstance()
    {
        return $this->_mockery_instanceMock;
    }

    public function __wakeup()
    {
        /**
         * This does not add __wakeup method support. It's a blind method and any
         * expected __wakeup work will NOT be performed. It merely cuts off
         * annoying errors where a __wakeup exists but is not essential when
         * mocking
         */
    }

    public function __destruct()
    {
        /**
         * Overrides real class destructor in case if class was created without original constructor
         */
    }

    /**
     * @param string $name Method name.
     *
     * @return \ReflectionMethod|null
     */
    public function mockery_getMethod($name)
    {
        foreach ($this->mockery_getMethods() as $method) {
            if ($method->getName() === $name) {
                return $method;
            }
        }

        return null;
    }

    /**
     * @param string $name Method name.
     *
     * @return mixed Generated return value based on the declared return value of the named method.
     */
    public function mockery_returnValueForMethod($name)
    {
        $reflectionMethod = $this->mockery_getMethod($name);

        if (! $reflectionMethod instanceof \ReflectionMethod) {
            return null;
        }

        $returnType = Reflector::getSimplestReturnType($reflectionMethod);

        switch ($returnType) {
            case null:     return null;
            case 'string': return '';
            case 'int':    return 0;
            case 'float':  return 0.0;
            case 'bool':   return false;
            case 'true':   return true;
            case 'false':   return false;

            case 'array':
            case 'iterable':
                return [];

            case 'callable':
            case '\Closure':
                return static function () : void {
                };

            case '\Traversable':
            case '\Generator':
                $generator = static function () {
                    yield;
                };
                return $generator();

            case 'void':
                return null;

            case 'static':
                return $this;

            case 'object':
                $mock = \Mockery::mock();
                if ($this->_mockery_ignoreMissingRecursive) {
                    $mock->shouldIgnoreMissing($this->_mockery_defaultReturnValue, true);
                }

                return $mock;

            default:
                $mock = \Mockery::mock($returnType);
                if ($this->_mockery_ignoreMissingRecursive) {
                    $mock->shouldIgnoreMissing($this->_mockery_defaultReturnValue, true);
                }

                return $mock;
        }
    }

    /**
     * @return ($method is null ? HigherOrderMessage : VerificationDirector)
     */
    #[\Override]
    public function shouldHaveReceived($method = null, $args = null)
    {
        if ($method === null) {
            return new HigherOrderMessage($this, 'shouldHaveReceived');
        }

        $verificationExpectation = new VerificationExpectation($this, $method);
        if (null !== $args) {
            $verificationExpectation->withArgs($args);
        }

        $verificationExpectation->atLeast()->once();

        $verificationDirector = new VerificationDirector(
            $this->_mockery_getReceivedMethodCalls(),
            $verificationExpectation
        );

        ++$this->_mockery_expectations_count;

        $verificationDirector->verify();

        return $verificationDirector;
    }

    /**
     * @return VerificationDirector
     */
    #[\Override]
    public function shouldHaveBeenCalled()
    {
        return $this->shouldHaveReceived('__invoke');
    }

    /**
     * @param null|string               $method
     * @param null|array<mixed>|\Closure $args
     *
     * @return ($method is null ? HigherOrderMessage : void)
     */
    #[\Override]
    public function shouldNotHaveReceived($method = null, $args = null)
    {
        if ($method === null) {
            return new HigherOrderMessage($this, 'shouldNotHaveReceived');
        }

        $verificationExpectation = new VerificationExpectation($this, $method);
        if (null !== $args) {
            $verificationExpectation->withArgs($args);
        }

        $verificationExpectation->never();

        $verificationDirector = new VerificationDirector(
            $this->_mockery_getReceivedMethodCalls(),
            $verificationExpectation
        );

        ++$this->_mockery_expectations_count;

        $verificationDirector->verify();

        return null;
    }

    /**
     * @param null|array<mixed> $args (optional)
     *
     * @return void
     */
    #[\Override]
    public function shouldNotHaveBeenCalled(?array $args = null)
    {
        return $this->shouldNotHaveReceived('__invoke', $args);
    }

    protected static function _mockery_handleStaticMethodCall($method, array $args)
    {
        $associatedRealObject = \Mockery::fetchMock(self::class);
        try {
            return $associatedRealObject->__call($method, $args);
        } catch (BadMethodCallException $badMethodCallException) {
            throw new BadMethodCallException(
                'Static method ' . $associatedRealObject->mockery_getName() . '::' . $method
                . '() does not exist on this mock object',
                0,
                $badMethodCallException
            );
        }
    }

    protected function _mockery_getReceivedMethodCalls()
    {
        return $this->_mockery_receivedMethodCalls ?: $this->_mockery_receivedMethodCalls = new ReceivedMethodCalls();
    }

    /**
     * Called when an instance Mock was created and its constructor is getting called
     *
     * @see \Mockery\Generator\StringManipulation\Pass\InstanceMockPass
     * @param array $args
     * @throws \Throwable
     * @return void
     */
    protected function _mockery_constructorCalled(array $args)
    {
        if (!isset($this->_mockery_expectations['__construct']) /* _mockery_handleMethodCall runs the other checks */) {
            return;
        }

        $this->_mockery_handleMethodCall('__construct', $args);
    }

    /**
     * @param string $method
     * @return ExpectationDirector|null
     */
    protected function _mockery_findExpectedMethodHandler($method)
    {
        if (isset($this->_mockery_expectations[$method])) {
            return $this->_mockery_expectations[$method];
        }

        $lowerCasedMockeryExpectations = array_change_key_case($this->_mockery_expectations, CASE_LOWER);
        $lowerCasedMethod = strtolower($method);

        return $lowerCasedMockeryExpectations[$lowerCasedMethod] ?? null;
    }

    /**
     * @param string $method
     * @param array<mixed> $args
     *
     * @return mixed
     * 
     * @throws \Throwable
     */
    protected function _mockery_handleMethodCall($method, array $args)
    {
        /**
         * Allow __clone and __construct to be mocked only if an expectations exists otherwise return void
         */
        // if (
        //     array_key_exists($method, ['__clone' => 1, '__construct' => 1]) &&
        //    !isset($this->_mockery_expectations[$method]) 
        // ) {
        //     return;
        // }

        $this->_mockery_getReceivedMethodCalls()->push(new MethodCall($method, $args));

        $reflectionMethod = $this->mockery_getMethod($method);
        if ($reflectionMethod && $reflectionMethod->isProtected() && !$this->_mockery_allowMockingProtectedMethods) {
            if ($reflectionMethod->isAbstract()) {
                return;
            }

            try {
                $prototype = $reflectionMethod->getPrototype();
                if ($prototype->isAbstract()) {
                    return;
                }
            } catch (\ReflectionException $re) {
                // noop - there is no hasPrototype method
            }

            if (null === $this->_mockery_parentClass) {
                $this->_mockery_parentClass = get_parent_class($this);
            }

            return call_user_func_array($this->_mockery_parentClass . '::' . $method, $args);
        }

        $expectationDirector = $this->_mockery_findExpectedMethodHandler($method);

        if ($expectationDirector instanceof ExpectationDirector && !$this->_mockery_disableExpectationMatching) {
            try {
                return $expectationDirector->call($args);
            } catch (NoMatchingExpectationException $e) {
                if (!$this->_mockery_ignoreMissing && !$this->_mockery_deferMissing) {
                    throw $e;
                }
            }
        }

        if (!is_null($this->_mockery_partial) &&
            (method_exists($this->_mockery_partial, $method) || method_exists($this->_mockery_partial, '__call'))) {
            return $this->_mockery_partial->{$method}(...$args);
        }

        if ($this->_mockery_deferMissing && is_callable($this->_mockery_parentClass . '::' . $method)
            && (!$this->hasMethodOverloadingInParentClass() || ($this->_mockery_parentClass && method_exists($this->_mockery_parentClass, $method)))) {
            return call_user_func_array($this->_mockery_parentClass . '::' . $method, $args);
        }

        if ($this->_mockery_deferMissing && $this->_mockery_parentClass && method_exists($this->_mockery_parentClass, '__call')) {
            return call_user_func($this->_mockery_parentClass . '::__call', $method, $args);
        }

        if ($method === '__toString') {
            // __toString is special because we force its addition to the class API regardless of the
            // original implementation.  Thus, we should always return a string rather than honor
            // _mockery_ignoreMissing and break the API with an error.
            return sprintf('%s#%s', self::class, spl_object_id($this));
        }

        if ($this->_mockery_ignoreMissing && (\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed() || (!is_null($this->_mockery_partial) && method_exists($this->_mockery_partial, $method)) || is_callable($this->_mockery_parentClass . '::' . $method))) {
            if ($this->_mockery_defaultReturnValue instanceof Undefined) {
                return $this->_mockery_defaultReturnValue->{$method}(...$args);
            }

            if (null === $this->_mockery_defaultReturnValue) {
                return $this->mockery_returnValueForMethod($method);
            }

            return $this->_mockery_defaultReturnValue;
        }

        $message = sprintf('Method %s::%s() does not exist on this mock object',self::class, $method);

        if ($reflectionMethod instanceof \ReflectionMethod) {
            $message = sprintf('Received %s::%s(), but no expectations were specified', self::class, $method);
        }

        $badMethodCallException = new BadMethodCallException($message);

        $this->_mockery_thrownExceptions[] = $badMethodCallException;

        throw $badMethodCallException;
    }

    /**
     * Uses reflection to get the list of all
     * methods within the current mock object
     *
     * @return list<\ReflectionMethod>
     */
    protected function mockery_getMethods()
    {
        $reflectionCacheEnabled = \Mockery::getConfiguration()->reflectionCacheEnabled();        
        if (static::$_mockery_methods && $reflectionCacheEnabled) {
            return static::$_mockery_methods;
        }

        if ($this->_mockery_partial !== null) {
            $reflected = new \ReflectionObject($this->_mockery_partial);
        } else {
            $reflected = new \ReflectionClass($this);
        }

        $methods = $reflected->getMethods();

        if ($reflectionCacheEnabled) {
            static::$_mockery_methods = $methods;
        }

        return $methods;
    }

    private function hasMethodOverloadingInParentClass(): bool
    {
        // if there's __call any name would be callable
        return is_callable($this->_mockery_parentClass . '::aFunctionNameThatNoOneWouldEverUseInRealLife12345');
    }

    /**
     * @return list<string>
     */
    private function getNonPublicMethods(): array
    {
        /** @var list<string> */
        $nonPublicMethods = array_map(
            static function (\ReflectionMethod $method): string {
                return $method->getName();
            },
            array_filter($this->mockery_getMethods(), static function (\ReflectionMethod $method): bool {
                return !$method->isPublic();
            })
        );

        return $nonPublicMethods;
    }
}
