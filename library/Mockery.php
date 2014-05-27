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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\ExpectationInterface;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use Mockery\Generator\StringManipulation\Pass\RemoveBuiltinMethodsThatAreFinalPass;
use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;

class Mockery
{
    const BLOCKS = 'Mockery_Forward_Blocks';

    /**
     * Global container to hold all mocks for the current unit test running
     *
     * @var \Mockery\Container
     */
    protected static $_container = null;

    /**
     * Global configuration handler containing configuration options
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
     * Static shortcut to \Mockery\Container::mock()
     *
     * @return \Mockery\MockInterface
     */
    public static function mock()
    {
        $args = func_get_args();
        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    public static function instanceMock()
    {
        $args = func_get_args();
        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static shortcut to \Mockery\Container::mock(), first argument names the
     * mock
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
     * Static shortcut to \Mockery\Container::self()
     *
     * @return \Mockery\MockInterface
     */
    public static function self()
    {
        if (is_null(self::$_container)) {
            throw new \LogicException("You have not declared any mocks yet");
        }

        return self::$_container->self();
    }

    /**
     * Static shortcut to closing up and verifying all mocks in the global
     * container, and resetting the container static variable to null
     *
     * @return void
     */
    public static function close()
    {
        if (is_null(self::$_container)) return;
        self::$_container->mockery_teardown();
        self::$_container->mockery_close();
        self::$_container = null;
    }

    /**
     * Static fetching of a mock associated with a name or explicit class poser
     */
    public static function fetchMock($name)
    {
        return self::$_container->fetchMock($name);
    }

    /**
     * Get the container
     */
    public static function getContainer()
    {
        if (self::$_container) {
            return self::$_container;
        }

        return self::$_container = new Mockery\Container(self::getGenerator(), self::getLoader());
    }

    public static function setGenerator(Generator $generator)
    {
        self::$_generator = $generator;
    }

    public static function getGenerator()
    {
        if (self::$_generator) {
            return self::$_generator;
        }

        self::$_generator = self::getDefaultGenerator();

        return self::$_generator;
    }

    public static function getDefaultGenerator()
    {
        $generator = new StringManipulationGenerator(array(
            new CallTypeHintPass(),
            new ClassPass(),
            new ClassNamePass(),
            new InstanceMockPass(),
            new InterfacePass(),
            new MethodDefinitionPass(),
            new RemoveBuiltinMethodsThatAreFinalPass(),
        ));

        $generator = new CachingGenerator($generator);

        return $generator;
    }

    public static function setLoader(Loader $loader)
    {
        self::$_loader = $loader;
    }

    public static function getLoader()
    {
        if (self::$_loader) {
            return self::$_loader;
        }

        self::$_loader = self::getDefaultLoader();

        return self::$_loader;
    }

    public static function getDefaultLoader()
    {
        return new EvalLoader();
    }


    /**
     * Set the container
     */
    public static function setContainer(Mockery\Container $container)
    {
        return self::$_container = $container;
    }

    /**
     * Reset the container to null
     */
    public static function resetContainer()
    {
        self::$_container = null;
    }

    /**
     * Return instance of ANY matcher
     *
     * @return
     */
    public static function any()
    {
        $return = new \Mockery\Matcher\Any();
        return $return;
    }

    /**
     * Return instance of TYPE matcher
     *
     * @return
     */
    public static function type($expected)
    {
        $return = new \Mockery\Matcher\Type($expected);
        return $return;
    }

    /**
     * Return instance of DUCKTYPE matcher
     *
     * @return
     */
    public static function ducktype()
    {
        $return = new \Mockery\Matcher\Ducktype(func_get_args());
        return $return;
    }

    /**
     * Return instance of SUBSET matcher
     *
     * @return
     */
    public static function subset(array $part)
    {
        $return = new \Mockery\Matcher\Subset($part);
        return $return;
    }

    /**
     * Return instance of CONTAINS matcher
     *
     * @return
     */
    public static function contains()
    {
        $return = new \Mockery\Matcher\Contains(func_get_args());
        return $return;
    }

    /**
     * Return instance of HASKEY matcher
     *
     * @return
     */
    public static function hasKey($key)
    {
        $return = new \Mockery\Matcher\HasKey($key);
        return $return;
    }

    /**
     * Return instance of HASVALUE matcher
     *
     * @return
     */
    public static function hasValue($val)
    {
        $return = new \Mockery\Matcher\HasValue($val);
        return $return;
    }

    /**
     * Return instance of CLOSURE matcher
     *
     * @return
     */
    public static function on($closure)
    {
        $return = new \Mockery\Matcher\Closure($closure);
        return $return;
    }

    /**
     * Return instance of MUSTBE matcher
     *
     * @return
     */
    public static function mustBe($expected)
    {
        $return = new \Mockery\Matcher\MustBe($expected);
        return $return;
    }

    /**
     * Return instance of NOT matcher
     *
     * @return
     */
    public static function not($expected)
    {
        $return = new \Mockery\Matcher\Not($expected);
        return $return;
    }

    /**
     * Return instance of ANYOF matcher
     *
     * @return
     */
    public static function anyOf()
    {
        $return = new \Mockery\Matcher\AnyOf(func_get_args());
        return $return;
    }

    /**
     * Return instance of NOTANYOF matcher
     *
     * @return
     */
    public static function notAnyOf()
    {
        $return = new \Mockery\Matcher\NotAnyOf(func_get_args());
        return $return;
    }

    /**
     * Get the global configuration container
     */
    public static function getConfiguration()
    {
        if (is_null(self::$_config)) {
            self::$_config = new \Mockery\Configuration;
        }
        return self::$_config;
    }

    /**
     * Utility method to format method name and args into a string
     *
     * @param string $method
     * @param array $args
     * @return string
     */
    public static function formatArgs($method, array $args = null)
    {
        $return = $method . '(';
        if ($args && !empty($args)) {
            $parts = array();
            foreach($args as $arg) {
                if (is_object($arg)) {
                    $parts[] = get_class($arg);
                } elseif (is_int($arg) || is_float($arg)) {
                    $parts[] = $arg;
                } elseif (is_array($arg)) {
                    $arg = preg_replace("{\s}", '', var_export($arg, true));
                    $parts[] = (strlen($arg) > 1000) ? substr($arg, 0, 1000).'...)' : $arg;
                } elseif (is_bool($arg)) {
                    $parts[] = $arg ? 'true' : 'false';
                } else {
                    $parts[] = '"' . (string) $arg . '"';
                }
            }
            $return .= implode(', ', $parts); // TODO: improve format

        }
        $return .= ')';
        return $return;
    }

    /**
     * Utility function to format objects to printable arrays
     *
     * @param array $args
     * @return string
     */
    public static function formatObjects(array $args = null)
    {
        static $formatting;
        if($formatting)
            return '[Recursion]';
        $formatting = true;
        $hasObjects = false;
        $parts = array();
        $return = 'Objects: (';
        if ($args && !empty($args)) {
            foreach($args as $arg) {
                if (is_object($arg)) {
                    $hasObjects = true;
                    $parts[get_class($arg)] = self::_objectToArray($arg);
                }
            }
        }
        $return .= var_export($parts, true);
        $return .= ')';
        $return = $hasObjects ? $return : '';
        $formatting = false;
        return $return;
    }

    /**
     * Utility function to turn public properties
     * and public get* and is* method values into an array
     *
     * @param object $object
     * @return string
     */
    private static function _objectToArray($object, $nesting = 3)
    {
        if ($nesting == 0) {
            return array('...');
        }
        $reflection = new \ReflectionClass(get_class($object));
        $properties = array();
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $publicProperty) {
            if ($publicProperty->isStatic()) continue;
            $name = $publicProperty->getName();
            $properties[$name] = self::_cleanupNesting($object->$name, $nesting);
        }

        $getters = array();
        foreach ($reflection->getMethods(\ReflectionProperty::IS_PUBLIC) as $publicMethod) {
            if ($publicMethod->isStatic()) continue;
            $name = $publicMethod->getName();
            $numberOfParameters = $publicMethod->getNumberOfParameters();
            if ((substr($name, 0, 3) === 'get' || substr($name, 0, 2) === 'is') && $numberOfParameters == 0) {
                try {
                    $getters[$name] = self::_cleanupNesting($object->$name(), $nesting);
                } catch(\Exception $e) {
                    $getters[$name] = '!! ' . get_class($e) . ': ' . $e->getMessage() . ' !!';
                }
            }
        }
        return array('class' => get_class($object), 'properties' => $properties, 'getters' => $getters);
    }

    private static function _cleanupNesting($arg, $nesting)
    {
        if (is_object($arg)) {
            $object = self::_objectToArray($arg, $nesting - 1);
            $object['class'] = get_class($arg);
            return $object;
        } elseif (is_array($arg)) {
            return self::_cleanupArray($arg, $nesting -1 );
        }
        return $arg;
    }

    private static function _cleanupArray($arg, $nesting = 3)
    {
        if ($nesting == 0) {
            return '...';
        }
        foreach ($arg as $key => $value) {
            if (is_array($value)) {
                $arg[$key] = self::_cleanupArray($value, $nesting -1);
            } elseif (is_object($value)) {
                $arg[$key] = self::_objectToArray($value, $nesting - 1);
            }
        }
        return $arg;
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
        $composite = new \Mockery\CompositeExpectation;
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach($arg as $k=>$v) {
                    $expectation = self::_buildDemeterChain($mock, $k, $add)->andReturn($v);
                    $composite->add($expectation);
                }
            } elseif (is_string($arg)) {
                $expectation = self::_buildDemeterChain($mock, $arg, $add);
                $composite->add($expectation);
            }
        }
        return $composite;
    }

    /**
     * Sets up expectations on the members of the CompositeExpectation and
     * builds up any demeter chain that was passed to shouldReceive
     *
     * @param \Mockery\MockInterface $mock
     * @param string $arg
     * @param callable $add
     * @throws Mockery\Exception
     * @return \Mockery\ExpectationDirector
     */
    protected static function _buildDemeterChain(\Mockery\MockInterface $mock, $arg, $add)
    {
        /** @var Mockery\Container $container */
        $container = $mock->mockery_getContainer();
        $methodNames = explode('->', $arg);
        reset($methodNames);
        if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()
        && !$mock->mockery_isAnonymous()
        && !in_array(current($methodNames), $mock->mockery_getMockableMethods())) {
            throw new \Mockery\Exception(
                'Mockery\'s configuration currently forbids mocking the method '
                . current($methodNames) . ' as it does not exist on the class or object '
                . 'being mocked'
            );
        }

        /** @var ExpectationInterface|null $exp */
        $exp = null;

        /** @var Callable $nextExp */
        $nextExp = function ($method) use ($add) {return $add($method);};
        while (true) {
            $method = array_shift($methodNames);
            $exp = $mock->mockery_getExpectationsFor($method);
            if (is_null($exp) || self::noMoreElementsInChain($methodNames)) {
                $exp = $nextExp($method);
                if (self::noMoreElementsInChain($methodNames)) {
                    break;
                }
                $mock = self::getNewDemeterMock($container, $method, $exp);
            } else {
                $demeterMockKey = $container->getKeyOfDemeterMockFor($method);
                if ($demeterMockKey) {
                    $mock = self::getExistingDemeterMock($container, $demeterMockKey);
                }
            }
            $nextExp = function ($n) use ($mock) {return $mock->shouldReceive($n);};
        }
        return $exp;
    }

    /**
     * @param \Mockery\Container $container
     * @param string $method
     * @param Mockery\ExpectationInterface $exp
     * @return \Mockery\Mock
     */
    private static function getNewDemeterMock(
        Mockery\Container $container,
        $method,
        Mockery\ExpectationInterface $exp
    )
    {
        $mock = $container->mock('demeter_' . $method);
        $exp->andReturn($mock);
        return $mock;
    }

    /**
     * @param \Mockery\Container $container
     * @param string $demeterMockKey
     * @return mixed
     */
    private static function getExistingDemeterMock(
        Mockery\Container $container,
        $demeterMockKey
    )
    {
        $mocks = $container->getMocks();
        $mock = $mocks[$demeterMockKey];
        return $mock;
    }

    /**
     * @param array $methodNames
     * @return bool
     */
    private static function noMoreElementsInChain(array $methodNames)
    {
        return empty($methodNames);
    }
}
