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

use Mockery\ExpectationInterface;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\RemoveDestructorPass;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use Mockery\Generator\StringManipulation\Pass\MagicMethodTypeHintsPass;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use Mockery\Generator\StringManipulation\Pass\RemoveBuiltinMethodsThatAreFinalPass;
use Mockery\Generator\StringManipulation\Pass\RemoveUnserializeForInternalSerializableClassesPass;
use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;

class Mockery
{
    const BLOCKS = 'Mockery_Forward_Blocks';

    /**
     * Global container to hold all mocks for the current unit test running.
     *
     * @var \Mockery\Container
     */
    protected static $_container = null;

    /**
     * Global configuration handler containing configuration options.
     *
     * @var \Mockery\Configuration
     */
    protected static $_config = null;

    /**
     * @var \Mockery\Generator\Generator
     */
    protected static $_generator;

    /**
     * @var \Mockery\Loader\Loader
     */
    protected static $_loader;

    /**
     * @var array
     */
    private static $_filesToCleanUp = [];

    /**
     * Static shortcut to \Mockery\Container::mock().
     *
     * @return \Mockery\MockInterface
     */
    public static function mock()
    {
        $args = func_get_args();

        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static and semantic shortcut for getting a mock from the container
     * and applying the spy's expected behavior into it.
     *
     * @return \Mockery\MockInterface
     */
    public static function spy()
    {
        $args = func_get_args();
        return call_user_func_array(array(self::getContainer(), 'mock'), $args)->shouldIgnoreMissing();
    }

    /**
     * Static and Semantic shortcut to \Mockery\Container::mock().
     *
     * @return \Mockery\MockInterface
     */
    public static function instanceMock()
    {
        $args = func_get_args();

        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static shortcut to \Mockery\Container::mock(), first argument names the mock.
     *
     * @return \Mockery\MockInterface
     */
    public static function namedMock()
    {
        $args = func_get_args();
        $name = array_shift($args);

        $builder = new MockConfigurationBuilder();
        $builder->setName($name);

        array_unshift($args, $builder);

        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static shortcut to \Mockery\Container::self().
     *
     * @throws LogicException
     *
     * @return \Mockery\MockInterface
     */
    public static function self()
    {
        if (is_null(self::$_container)) {
            throw new \LogicException('You have not declared any mocks yet');
        }

        return self::$_container->self();
    }

    /**
     * Static shortcut to closing up and verifying all mocks in the global
     * container, and resetting the container static variable to null.
     *
     * @return void
     */
    public static function close()
    {
        foreach (self::$_filesToCleanUp as $fileName) {
            @unlink($fileName);
        }
        self::$_filesToCleanUp = [];

        if (is_null(self::$_container)) {
            return;
        }

        self::$_container->mockery_teardown();
        self::$_container->mockery_close();
        self::$_container = null;
    }

    /**
     * Static fetching of a mock associated with a name or explicit class poser.
     *
     * @param $name
     *
     * @return \Mockery\Mock
     */
    public static function fetchMock($name)
    {
        return self::$_container->fetchMock($name);
    }

    /**
     * Lazy loader and getter for
     * the container property.
     *
     * @return Mockery\Container
     */
    public static function getContainer()
    {
        if (is_null(self::$_container)) {
            self::$_container = new Mockery\Container(self::getGenerator(), self::getLoader());
        }

        return self::$_container;
    }

    /**
     * Setter for the $_generator static propery.
     *
     * @param \Mockery\Generator\Generator $generator
     */
    public static function setGenerator(Generator $generator)
    {
        self::$_generator = $generator;
    }

    /**
     * Lazy loader method and getter for
     * the generator property.
     *
     * @return Generator
     */
    public static function getGenerator()
    {
        if (is_null(self::$_generator)) {
            self::$_generator = self::getDefaultGenerator();
        }

        return self::$_generator;
    }

    /**
     * Creates and returns a default generator
     * used inside this class.
     *
     * @return CachingGenerator
     */
    public static function getDefaultGenerator()
    {
        $generator = new StringManipulationGenerator(array(
            new CallTypeHintPass(),
            new MagicMethodTypeHintsPass(),
            new ClassPass(),
            new ClassNamePass(),
            new InstanceMockPass(),
            new InterfacePass(),
            new MethodDefinitionPass(),
            new RemoveUnserializeForInternalSerializableClassesPass(),
            new RemoveBuiltinMethodsThatAreFinalPass(),
            new RemoveDestructorPass(),
        ));

        return new CachingGenerator($generator);
    }

    /**
     * Setter for the $_loader static property.
     *
     * @param Loader $loader
     */
    public static function setLoader(Loader $loader)
    {
        self::$_loader = $loader;
    }

    /**
     * Lazy loader method and getter for
     * the $_loader property.
     *
     * @return Loader
     */
    public static function getLoader()
    {
        if (is_null(self::$_loader)) {
            self::$_loader = self::getDefaultLoader();
        }

        return self::$_loader;
    }

    /**
     * Gets an EvalLoader to be used as default.
     *
     * @return EvalLoader
     */
    public static function getDefaultLoader()
    {
        return new EvalLoader();
    }

    /**
     * Set the container.
     *
     * @param \Mockery\Container $container
     *
     * @return \Mockery\Container
     */
    public static function setContainer(Mockery\Container $container)
    {
        return self::$_container = $container;
    }

    /**
     * Reset the container to null.
     *
     * @return void
     */
    public static function resetContainer()
    {
        self::$_container = null;
    }

    /**
     * Return instance of ANY matcher.
     *
     * @return \Mockery\Matcher\Any
     */
    public static function any()
    {
        return new \Mockery\Matcher\Any();
    }

    /**
     * Return instance of TYPE matcher.
     *
     * @param $expected
     *
     * @return \Mockery\Matcher\Type
     */
    public static function type($expected)
    {
        return new \Mockery\Matcher\Type($expected);
    }

    /**
     * Return instance of DUCKTYPE matcher.
     *
     * @return \Mockery\Matcher\Ducktype
     */
    public static function ducktype()
    {
        return new \Mockery\Matcher\Ducktype(func_get_args());
    }

    /**
     * Return instance of SUBSET matcher.
     *
     * @param array $part
     *
     * @return \Mockery\Matcher\Subset
     */
    public static function subset(array $part)
    {
        return new \Mockery\Matcher\Subset($part);
    }

    /**
     * Return instance of CONTAINS matcher.
     *
     * @return \Mockery\Matcher\Contains
     */
    public static function contains()
    {
        return new \Mockery\Matcher\Contains(func_get_args());
    }

    /**
     * Return instance of HASKEY matcher.
     *
     * @param $key
     *
     * @return \Mockery\Matcher\HasKey
     */
    public static function hasKey($key)
    {
        return new \Mockery\Matcher\HasKey($key);
    }

    /**
     * Return instance of HASVALUE matcher.
     *
     * @param $val
     *
     * @return \Mockery\Matcher\HasValue
     */
    public static function hasValue($val)
    {
        return new \Mockery\Matcher\HasValue($val);
    }

    /**
     * Return instance of CLOSURE matcher.
     *
     * @param $closure
     *
     * @return \Mockery\Matcher\Closure
     */
    public static function on($closure)
    {
        return new \Mockery\Matcher\Closure($closure);
    }

    /**
     * Return instance of MUSTBE matcher.
     *
     * @param $expected
     *
     * @return \Mockery\Matcher\MustBe
     */
    public static function mustBe($expected)
    {
        return new \Mockery\Matcher\MustBe($expected);
    }

    /**
     * Return instance of NOT matcher.
     *
     * @param $expected
     *
     * @return \Mockery\Matcher\Not
     */
    public static function not($expected)
    {
        return new \Mockery\Matcher\Not($expected);
    }

    /**
     * Return instance of ANYOF matcher.
     *
     * @return \Mockery\Matcher\AnyOf
     */
    public static function anyOf()
    {
        return new \Mockery\Matcher\AnyOf(func_get_args());
    }

    /**
     * Return instance of NOTANYOF matcher.
     *
     * @return \Mockery\Matcher\NotAnyOf
     */
    public static function notAnyOf()
    {
        return new \Mockery\Matcher\NotAnyOf(func_get_args());
    }

    /**
     * Lazy loader and Getter for the global
     * configuration container.
     *
     * @return \Mockery\Configuration
     */
    public static function getConfiguration()
    {
        if (is_null(self::$_config)) {
            self::$_config = new \Mockery\Configuration();
        }

        return self::$_config;
    }

    /**
     * Utility method to format method name and arguments into a string.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return string
     */
    public static function formatArgs($method, array $arguments = null)
    {
        if (is_null($arguments)) {
            return $method . '()';
        }

        $formattedArguments = array();
        foreach ($arguments as $argument) {
            $formattedArguments[] = self::formatArgument($argument);
        }

        return $method . '(' . implode(', ', $formattedArguments) . ')';
    }

    /**
     * Gets the string representation
     * of any passed argument.
     *
     * @param $argument
     * @param $depth
     *
     * @return string
     */
    private static function formatArgument($argument, $depth = 0)
    {
        if (is_object($argument)) {
            return 'object(' . get_class($argument) . ')';
        }

        if (is_int($argument) || is_float($argument)) {
            return $argument;
        }

        if (is_array($argument)) {
            if ($depth === 1) {
                $argument = '[...]';
            } else {
                $sample = array();
                foreach ($argument as $key => $value) {
                    $key = is_int($key) ? $key : "'$key'";
                    $value = self::formatArgument($value, $depth + 1);
                    $sample[] = "$key => $value";
                }

                $argument = "[".implode(", ", $sample)."]";
            }

            return ((strlen($argument) > 1000) ? substr($argument, 0, 1000).'...]' : $argument);
        }

        if (is_bool($argument)) {
            return $argument ? 'true' : 'false';
        }

        if (is_resource($argument)) {
            return 'resource(...)';
        }

        if (is_null($argument)) {
            return 'NULL';
        }

        return "'".(string) $argument."'";
    }

    /**
     * Utility function to format objects to printable arrays.
     *
     * @param array $objects
     *
     * @return string
     */
    public static function formatObjects(array $objects = null)
    {
        static $formatting;

        if ($formatting) {
            return '[Recursion]';
        }

        if (is_null($objects)) {
            return '';
        }

        $objects = array_filter($objects, 'is_object');
        if (empty($objects)) {
            return '';
        }

        $formatting = true;
        $parts = array();

        foreach ($objects as $object) {
            $parts[get_class($object)] = self::objectToArray($object);
        }

        $formatting = false;

        return 'Objects: ( ' . var_export($parts, true) . ')';
    }

    /**
     * Utility function to turn public properties and public get* and is* method values into an array.
     *
     * @param     $object
     * @param int $nesting
     *
     * @return array
     */
    private static function objectToArray($object, $nesting = 3)
    {
        if ($nesting == 0) {
            return array('...');
        }

        return array(
            'class' => get_class($object),
            'properties' => self::extractInstancePublicProperties($object, $nesting),
            'getters' => self::extractGetters($object, $nesting)
        );
    }

    /**
     * Returns all public instance properties.
     *
     * @param $object
     * @param $nesting
     *
     * @return array
     */
    private static function extractInstancePublicProperties($object, $nesting)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $cleanedProperties = array();

        foreach ($properties as $publicProperty) {
            if (!$publicProperty->isStatic()) {
                $name = $publicProperty->getName();
                $cleanedProperties[$name] = self::cleanupNesting($object->$name, $nesting);
            }
        }

        return $cleanedProperties;
    }

    /**
     * Returns all object getters.
     *
     * @param $object
     * @param $nesting
     *
     * @return array
     */
    private static function extractGetters($object, $nesting)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $getters = array();

        foreach ($publicMethods as $publicMethod) {
            $name = $publicMethod->getName();
            $irrelevantName = (substr($name, 0, 3) !== 'get' && substr($name, 0, 2) !== 'is');
            $isStatic = $publicMethod->isStatic();
            $numberOfParameters = $publicMethod->getNumberOfParameters();

            if ($irrelevantName || $numberOfParameters != 0 || $isStatic) {
                continue;
            }

            try {
                $getters[$name] = self::cleanupNesting($object->$name(), $nesting);
            } catch (\Exception $e) {
                $getters[$name] = '!! ' . get_class($e) . ': ' . $e->getMessage() . ' !!';
            }
        }

        return $getters;
    }

    /**
     * Utility method used for recursively generating
     * an object or array representation.
     *
     * @param $argument
     * @param $nesting
     *
     * @return mixed
     */
    private static function cleanupNesting($argument, $nesting)
    {
        if (is_object($argument)) {
            $object = self::objectToArray($argument, $nesting - 1);
            $object['class'] = get_class($argument);

            return $object;
        }

        if (is_array($argument)) {
            return self::cleanupArray($argument, $nesting - 1);
        }

        return $argument;
    }

    /**
     * Utility method for recursively
     * gerating a representation
     * of the given array.
     *
     * @param array $argument
     * @param int $nesting
     *
     * @return mixed
     */
    private static function cleanupArray($argument, $nesting = 3)
    {
        if ($nesting == 0) {
            return '...';
        }

        foreach ($argument as $key => $value) {
            if (is_array($value)) {
                $argument[$key] = self::cleanupArray($value, $nesting - 1);
            } elseif (is_object($value)) {
                $argument[$key] = self::objectToArray($value, $nesting - 1);
            }
        }

        return $argument;
    }

    /**
     * Utility function to parse shouldReceive() arguments and generate
     * expectations from such as needed.
     *
     * @param Mockery\MockInterface $mock
     * @param array $args
     * @param callable $add
     * @return \Mockery\CompositeExpectation
     */
    public static function parseShouldReturnArgs(\Mockery\MockInterface $mock, $args, $add)
    {
        $composite = new \Mockery\CompositeExpectation();

        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $k => $v) {
                    $expectation = self::buildDemeterChain($mock, $k, $add)->andReturn($v);
                    $composite->add($expectation);
                }
            } elseif (is_string($arg)) {
                $expectation = self::buildDemeterChain($mock, $arg, $add);
                $composite->add($expectation);
            }
        }

        return $composite;
    }

    /**
     * Sets up expectations on the members of the CompositeExpectation and
     * builds up any demeter chain that was passed to shouldReceive.
     *
     * @param \Mockery\MockInterface $mock
     * @param string $arg
     * @param callable $add
     * @throws Mockery\Exception
     * @return \Mockery\ExpectationDirector
     */
    protected static function buildDemeterChain(\Mockery\MockInterface $mock, $arg, $add)
    {
        /** @var Mockery\Container $container */
        $container = $mock->mockery_getContainer();
        $methodNames = explode('->', $arg);
        reset($methodNames);

        if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()
            && !$mock->mockery_isAnonymous()
            && !in_array(current($methodNames), $mock->mockery_getMockableMethods())
        ) {
            throw new \Mockery\Exception(
                'Mockery\'s configuration currently forbids mocking the method '
                . current($methodNames) . ' as it does not exist on the class or object '
                . 'being mocked'
            );
        }

        /** @var ExpectationInterface|null $expectations */
        $expectations = null;

        /** @var Callable $nextExp */
        $nextExp = function ($method) use ($add) {
            return $add($method);
        };

        while (true) {
            $method = array_shift($methodNames);
            $expectations = $mock->mockery_getExpectationsFor($method);

            if (is_null($expectations) || self::noMoreElementsInChain($methodNames)) {
                $expectations = $nextExp($method);
                if (self::noMoreElementsInChain($methodNames)) {
                    break;
                }

                $mock = self::getNewDemeterMock($container, $method, $expectations);
            } else {
                $demeterMockKey = $container->getKeyOfDemeterMockFor($method);
                if ($demeterMockKey) {
                    $mock = self::getExistingDemeterMock($container, $demeterMockKey);
                }
            }

            $nextExp = function ($n) use ($mock) {
                return $mock->shouldReceive($n);
            };
        }

        return $expectations;
    }

    /**
     * Gets a new demeter configured
     * mock from the container.
     *
     * @param \Mockery\Container $container
     * @param string $method
     * @param Mockery\ExpectationInterface $exp
     *
     * @return \Mockery\Mock
     */
    private static function getNewDemeterMock(
        Mockery\Container $container,
        $method,
        Mockery\ExpectationInterface $exp
    ) {
        $mock = $container->mock('demeter_' . $method);
        $exp->andReturn($mock);

        return $mock;
    }

    /**
     * Gets an specific demeter mock from
     * the ones kept by the container.
     *
     * @param \Mockery\Container $container
     * @param string $demeterMockKey
     *
     * @return mixed
     */
    private static function getExistingDemeterMock(
        Mockery\Container $container,
        $demeterMockKey
    ) {
        $mocks = $container->getMocks();
        $mock = $mocks[$demeterMockKey];

        return $mock;
    }

    /**
     * Checks if the passed array representing a demeter
     * chain with the method names is empty.
     *
     * @param array $methodNames
     *
     * @return bool
     */
    private static function noMoreElementsInChain(array $methodNames)
    {
        return empty($methodNames);
    }

    /**
     * Register a file to be deleted on tearDown.
     *
     * @param string $fileName
     */
    public static function registerFileForCleanUp($fileName)
    {
        self::$_filesToCleanUp[] = $fileName;
    }
}
