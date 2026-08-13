<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;
use Exception as PHPException;
use LogicException;
use Mockery;
use Mockery\Exception\BadMethodCallException;
use Mockery\Exception\InvalidOrderException;
use Mockery\Exception\RuntimeException;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Loader\Loader as LoaderInterface;
use ReflectionClass;
use stdClass;
use Throwable;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_pop;
use function array_shift;
use function array_values;
use function class_exists;
use function count;
use function explode;
use function get_class;
use function interface_exists;
use function is_array;
use function is_object;
use function is_string;
use function md5;
use function preg_grep;
use function preg_match;
use function range;
use function reset;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function substr;
use function trait_exists;
use function trim;

/**
 * Container for mock objects
 */
class Container
{
    public const BLOCKS = Mockery::BLOCKS;

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
     * @var Generator
     */
    protected $_generator;

    /**
     * Ordered groups
     *
     * @var array<string,int>
     */
    protected $_groups = [];

    /**
     * @var LoaderInterface
     */
    protected $_loader;

    /**
     * Store of mock objects
     *
     * @var array<class-string<MockInterface>,MockInterface>
     */
    protected $_mocks = [];

    /**
     * @var array<string,string>
     */
    protected $_namedMocks = [];

    /**
     * @var Instantiator
     */
    protected $instantiator;

    public function __construct(?Generator $generator = null, ?LoaderInterface $loader = null, ?Instantiator $instantiator = null)
    {
        $this->_generator = $generator instanceof Generator ? $generator : Mockery::getDefaultGenerator();
        $this->_loader = $loader instanceof LoaderInterface ? $loader : Mockery::getDefaultLoader();
        $this->instantiator = $instantiator instanceof Instantiator ? $instantiator : new Instantiator();
    }

    /**
     * Return a specific remembered mock according to the array index it
     * was stored to in this container instance
     *
     * @param  class-string       $reference
     * @return null|MockInterface
     */
    public function fetchMock($reference)
    {
        return $this->_mocks[$reference] ?? null;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->_generator;
    }

    /**
     * @param  string      $method
     * @param  string      $parent
     * @return null|string
     */
    public function getKeyOfDemeterMockFor($method, $parent)
    {
        $keys = array_keys($this->_mocks);

        $match = preg_grep(sprintf('#__demeter_%s_%s$#', md5($parent), $method), $keys);
        if (false === $match) {
            return null;
        }

        if ([] === $match) {
            return null;
        }

        return array_values($match)[0];
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->_loader;
    }

    /**
     * @return array<class-string<MockInterface>,MockInterface>
     */
    public function getMocks()
    {
        return $this->_mocks;
    }

    /**
     * @return void
     */
    public function instanceMock() {}

    /**
     * see http://php.net/manual/en/language.oop5.basic.php
     *
     * @param  string $className
     * @return bool
     */
    public function isValidClassName($className)
    {
        if (trim($className) === '') {
            return false;
        }

        if ('\\' === $className[0]) {
            $className = substr($className, 1); // remove the first backslash
        }

        // all the namespaces and class name should match the regex
        return array_filter(
            explode('\\', $className),
            static function ($name): bool {
                return ! preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
            }
        ) === [];
    }

    /**
     * Generates a new mock object for this container
     *
     * I apologies in advance for this. A God Method just fits the API which
     * doesn't require differentiating between classes, interfaces, abstracts,
     * names or partials - just so long as it's something that can be mocked.
     * I'll refactor it one day so it's easier to follow.
     *
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    public function mock(...$args)
    {
        [
            $expectationClosure,
            $builder,
            $partialMethods,
            $quickDefinitions,
            $constructorArgs,
            $blocks
        ] = $this->parseArguments($args);

        $builder->addBlackListedMethods($blocks);

        if (null !== $constructorArgs) {
            $builder->addBlackListedMethod('__construct');
        } else {
            $builder->setMockOriginalDestructor(true);
        }

        if (null !== $partialMethods && null === $constructorArgs) {
            $constructorArgs = [];
        }

        $mockConfiguration = $builder->getMockConfiguration();

        $this->checkForNamedMockClashes($mockConfiguration);

        $className = $this->generateMock($mockConfiguration);

        $mock = $this->initializeMock($className, $constructorArgs, $mockConfiguration);

        if ([] !== $quickDefinitions) {
            if (
                Mockery::getConfiguration()
                    ->getQuickDefinitions()
                    ->shouldBeCalledAtLeastOnce()
            ) {
                $mock->shouldReceive($quickDefinitions)
                    ->atLeast()
                    ->once();
            } else {
                $mock->shouldReceive($quickDefinitions)
                    ->byDefault();
            }
        }

        if ($expectationClosure instanceof Closure) {
            $expectationClosure($mock);
        }

        return $this->rememberMock($mock);
    }

    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder()
    {
        return ++$this->_allocatedOrder;
    }

    /**
     * Reset the container to its original state
     *
     * @return void
     */
    public function mockery_close()
    {
        foreach ($this->_mocks as $mock) {
            $mock->mockery_teardown();
        }

        $this->_mocks = [];
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
     * Gets the count of expectations on the mocks
     *
     * @return int
     */
    public function mockery_getExpectationCount()
    {
        $count = 0;
        foreach ($this->_mocks as $mock) {
            $count += $mock->mockery_getExpectationCount();
        }

        return $count;
    }

    /**
     * Fetch array of ordered groups
     *
     * @return array<string,int>
     */
    public function mockery_getGroups()
    {
        return $this->_groups;
    }

    /**
     * Set current ordered number
     *
     * @param  int $order
     * @return int The current order number that was set
     */
    public function mockery_setCurrentOrder($order)
    {
        return $this->_currentOrder = $order;
    }

    /**
     * Set ordering for a group
     *
     * @param  string $group
     * @param  int    $order
     * @return void
     */
    public function mockery_setGroup($group, $order)
    {
        $this->_groups[$group] = $order;
    }

    /**
     * Tear down tasks for this container
     *
     * @return void
     *
     * @throws PHPException
     */
    public function mockery_teardown()
    {
        try {
            $this->mockery_verify();
        } catch (PHPException $phpException) {
            $this->mockery_close();

            throw $phpException;
        }
    }

    /**
     * Retrieves all exceptions thrown by mocks
     *
     * @return array<BadMethodCallException>
     */
    public function mockery_thrownExceptions()
    {
        /** @var array<BadMethodCallException> $exceptions */
        $exceptions = [];

        foreach ($this->_mocks as $mock) {
            foreach ($mock->mockery_thrownExceptions() as $exception) {
                $exceptions[] = $exception;
            }
        }

        return $exceptions;
    }

    /**
     * Validate the current mock's ordering
     *
     * @param  string $method
     * @param  int    $order
     * @return void
     *
     * @throws Exception
     */
    public function mockery_validateOrder($method, $order, LegacyMockInterface $mock)
    {
        if ($order < $this->_currentOrder) {
            $invalidOrderException = new InvalidOrderException(
                sprintf(
                    'Method %s called out of order: expected order %d, was %d',
                    $method,
                    $order,
                    $this->_currentOrder
                )
            );

            $invalidOrderException->setMock($mock)
                ->setMethodName($method)
                ->setExpectedOrder($order)
                ->setActualOrder($this->_currentOrder);

            throw $invalidOrderException;
        }

        $this->mockery_setCurrentOrder($order);
    }

    /**
     * Verify the container mocks
     *
     * @return void
     */
    public function mockery_verify()
    {
        foreach ($this->_mocks as $mock) {
            $mock->mockery_verify();
        }
    }

    /**
     * Store a mock and set its container reference
     *
     * @param  MockInterface $mock
     * @return MockInterface
     */
    public function rememberMock(LegacyMockInterface $mock)
    {
        $class = get_class($mock);

        if (! array_key_exists($class, $this->_mocks)) {
            return $this->_mocks[$class] = $mock;
        }

        /**
         * This condition triggers for an instance mock
         * where origin mock is already remembered
         */
        return $this->_mocks[] = $mock;
    }

    /**
     * Retrieve the last remembered mock object,
     * which is the same as saying retrieve the current mock being programmed where you have yet to call mock()
     * to change it thus why the method name is "self" since it will be used during the programming of the same mock.
     *
     * @return MockInterface
     *
     * @throws LogicException
     */
    public function self()
    {
        $mocks = array_values($this->_mocks);

        if ([] === $mocks) {
            // No mocks have been created yet
            throw new LogicException('You have not declared any mocks yet');
        }

        $index = count($mocks) - 1;

        return $mocks[$index];
    }

    /**
     * @template TObject of object
     *
     * @param  class-string<TObject> $mockName
     * @param  null|list<mixed>      $constructorArgs
     * @return TObject
     *
     * @throws Throwable
     */
    protected function _getInstance($mockName, $constructorArgs = null)
    {
        if (null !== $constructorArgs) {
            return (new ReflectionClass($mockName))->newInstanceArgs($constructorArgs);
        }

        try {
            $instance = $this->instantiator->instantiate($mockName);
        } catch (PHPException $phpException) {
            /** @var class-string<TObject> $internalMockName */
            $internalMockName = $mockName . '_Internal';

            if (! class_exists($internalMockName)) {
                eval(sprintf(
                    'class %s extends %s { public function __construct() {} }',
                    $internalMockName,
                    $mockName
                ));
            }

            $instance = new $internalMockName();
        }

        /** @var TObject $instance */
        return $instance;
    }

    /**
     * @param  MockConfiguration $config
     * @return void
     *
     * @throws Throwable
     */
    protected function checkForNamedMockClashes($config)
    {
        $name = $config->getName();

        if (null === $name) {
            return;
        }

        $hash = $config->getHash();

        if (array_key_exists($name, $this->_namedMocks) && $hash !== $this->_namedMocks[$name]) {
            throw new Exception(
                sprintf("The mock named '%s' has been already defined with a different mock configuration", $name)
            );
        }

        $this->_namedMocks[$name] = $hash;
    }

    private function createBuilder(array &$arguments): MockConfigurationBuilder
    {
        foreach ($arguments as $key => $argument) {
            if (! $argument instanceof MockConfigurationBuilder) {
                continue;
            }

            unset($arguments[$key]);

            return $argument;
        }

        return new MockConfigurationBuilder();
    }

    /**
     * @return class-string
     *
     * @throws Throwable
     */
    private function generateMock(MockConfiguration $mockConfiguration): string
    {
        $mockDefinition = $this->getGenerator()
            ->generate($mockConfiguration);

        $className = $mockDefinition->getClassName();

        if (class_exists($className, $attemptAutoload = false)) {
            $reflectionClass = new ReflectionClass($className);

            if (! $reflectionClass->implementsInterface(LegacyMockInterface::class)) {
                throw new RuntimeException(sprintf('Could not load mock %s, class already exists', $className));
            }
        }

        $this->getLoader()
            ->load($mockDefinition);

        return $className;
    }

    /**
     * @return null|Closure(MockInterface):void
     */
    private function handleClosure(array &$arguments): ?Closure
    {
        if (count($arguments) < 2) {
            return null;
        }

        $argument = array_pop($arguments);

        if ($argument instanceof Closure) {
            /** @var Closure(MockInterface):void $argument */
            return $argument;
        }

        $arguments[] = $argument;

        return null;
    }

    private function initializeBuilder(array &$arguments): MockConfigurationBuilder
    {
        $configuration = Mockery::getConfiguration();

        return $this->createBuilder($arguments)
            ->setParameterOverrides($configuration->getInternalClassMethodParamMaps())
            ->setConstantsMap($configuration->getConstantsMap());
    }

    /**
     * @param class-string     $className
     * @param null|list<mixed> $constructorArgs
     */
    private function initializeMock(
        string $className,
        ?array $constructorArgs,
        MockConfiguration $mockConfiguration
    ): object {
        $mock = $this->_getInstance($className, $constructorArgs);
        $mock->mockery_init($this, $mockConfiguration->getTargetObject(), $mockConfiguration->isInstanceMock());

        return $mock;
    }

    private function parseArguments(array &$arguments): array
    {
        $blocks = [];
        $constructorArgs = null;
        $partialMethods = null;
        $quickDefinitions = [];
        $expectationClosure = $this->handleClosure($arguments);
        $builder = $this->initializeBuilder($arguments);

        while ([] !== $arguments) {
            $argument = array_shift($arguments);

            if (is_string($argument)) {
                $this->parseStringArgument($argument, $builder, $partialMethods);

                continue;
            }

            if (is_object($argument)) {
                $builder->addTarget($argument);

                continue;
            }

            if (is_array($argument)) {
                $this->parseArrayArgument($argument, $quickDefinitions, $constructorArgs, $blocks);

                continue;
            }

            throw new Exception(sprintf('Unable to parse arguments sent to %s::mock()', static::class));
        }

        return [$expectationClosure, $builder, $partialMethods, $quickDefinitions, $constructorArgs, $blocks];
    }

    private function parseArrayArgument(
        array $argument,
        array &$quickDefinitions,
        ?array &$constructorArgs,
        array &$blocks
    ): void {
        if ([] !== $argument && array_keys($argument) !== range(0, count($argument) - 1)) {
            if (array_key_exists(self::BLOCKS, $argument)) {
                $blocks = $argument[self::BLOCKS];
            }
            unset($argument[self::BLOCKS]);
            $quickDefinitions = $argument;
        } else {
            $constructorArgs = $argument;
        }
    }

    private function parseStringArgument(
        string $arguments,
        MockConfigurationBuilder $builder,
        ?array &$partialMethods
    ): void {
        foreach (explode('|', $arguments) as $type) {
            if ('null' === $arguments) {
                continue;
            }

            if (str_contains($type, ',') && ! str_contains($type, ']')) {
                $interfaces = explode(',', str_replace(' ', '', $type));
                /** @var list<class-string> $interfaces */
                $builder->addTargets($interfaces);

                continue;
            }

            if (str_starts_with($type, 'alias:')) {
                $builder->addTarget(stdClass::class);
                $builder->setName(substr($type, 6));

                continue;
            }

            if (str_starts_with($type, 'overload:')) {
                $builder->addTarget(stdClass::class);
                $builder->setInstanceMock(true);
                $builder->setName(substr($type, 9));

                continue;
            }

            if (str_ends_with($type, ']')) {
                $parts = explode('[', $type);
                $class = $parts[0];

                if (! class_exists($class, true) && ! interface_exists($class, true)) {
                    throw new Exception('Can only create a partial mock from an existing class or interface');
                }

                $builder->addTarget($class);
                $partialMethods = array_filter(explode(',', strtolower(rtrim(str_replace(' ', '', $parts[1]), ']'))));
                foreach ($partialMethods as $partialMethod) {
                    if ('!' === $partialMethod[0]) {
                        $builder->addBlackListedMethod(substr($partialMethod, 1));
                    } else {
                        $builder->addWhiteListedMethod($partialMethod);
                    }
                }

                continue;
            }

            if (class_exists($type, true) || interface_exists($type, true) || trait_exists($type, true)) {
                $builder->addTarget($type);

                continue;
            }

            if (! Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
                throw new Exception(sprintf("Mockery can't find '%s' so can't mock it", $type));
            }

            if (! $this->isValidClassName($type)) {
                throw new Exception('Class name contains invalid characters');
            }

            /** @var class-string $type */
            $builder->addTarget($type);

            break;
        }
    }
}
