<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

use Mockery\ClosureWrapper;
use Mockery\ExpectationInterface;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\MockNameBuilder;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;
use Mockery\Matcher\IsEqual;
use Mockery\Matcher\IsSame;
use Mockery\Matcher\MatcherInterface;
use Mockery\Reflector;

class Mockery
{
    const BLOCKS = 'Mockery_Forward_Blocks';

    /**
     * Global container to hold all mocks for the current unit test running.
     *
     * @var \Mockery\Container|null
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
     * Defines the global helper functions
     *
     * @return void
     */
    public static function globalHelpers()
    {
        require_once __DIR__ . '/helpers.php';
    }

    /**
     * @return array
     *
     * @deprecated since 1.3.2 and will be removed in 2.0.
     */
    public static function builtInTypes()
    {
        return array(
            'array',
            'bool',
            'callable',
            'float',
            'int',
            'iterable',
            'object',
            'self',
            'string',
            'void',
        );
    }

    /**
     * @param string $type
     * @return bool
     *
     * @deprecated since 1.3.2 and will be removed in 2.0.
     */
    public static function isBuiltInType($type)
    {
        return in_array($type, \Mockery::builtInTypes());
    }

    /**
     * Static shortcut to \Mockery\Container::mock().
     *
     * @param mixed ...$args
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    public static function mock(...$args)
    {
        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static and semantic shortcut for getting a mock from the container
     * and applying the spy's expected behavior into it.
     *
     * @param mixed ...$args
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    public static function spy(...$args)
    {
        if (count($args) && $args[0] instanceof \Closure) {
            $args[0] = new ClosureWrapper($args[0]);
        }

        return call_user_func_array(array(self::getContainer(), 'mock'), $args)->shouldIgnoreMissing();
    }

    /**
     * @param string $class
     * @param array $items
     * @return \Mockery\MockInterface
     * @throws \Mockery\Exception\RuntimeException
     */
    public static function stubTraversable($class, array $items)
    {
        return self::getContainer()->stubTraversable($class, $items);
    }

    /**
     * Static and Semantic shortcut to \Mockery\Container::mock().
     *
     * @param mixed ...$args
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    public static function instanceMock(...$args)
    {
        return call_user_func_array(array(self::getContainer(), 'mock'), $args);
    }

    /**
     * Static shortcut to \Mockery\Container::mock(), first argument names the mock.
     *
     * @param mixed ...$args
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    public static function namedMock(...$args)
    {
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
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
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

        $container = self::$_container;
        self::$_container = null;

        $container->mockery_teardown();
        $container->mockery_close();
    }

    /**
     * Static fetching of a mock associated with a name or explicit class poser.
     *
     * @param string $name
     *
     * @return \Mockery\Mock
     */
    public static function fetchMock($name)
    {
        return self::getContainer()->fetchMock($name);
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
     * Setter for the $_generator static property.
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
        return new CachingGenerator(StringManipulationGenerator::withDefaultPasses());
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
     * Return instance of AndAnyOtherArgs matcher.
     *
     * An alternative name to `andAnyOtherArgs` so
     * the API stays closer to `any` as well.
     *
     * @return \Mockery\Matcher\AndAnyOtherArgs
     */
    public static function andAnyOthers()
    {
        return new \Mockery\Matcher\AndAnyOtherArgs();
    }

    /**
     * Return instance of AndAnyOtherArgs matcher.
     *
     * @return \Mockery\Matcher\AndAnyOtherArgs
     */
    public static function andAnyOtherArgs()
    {
        return new \Mockery\Matcher\AndAnyOtherArgs();
    }

    /**
     * Return instance of TYPE matcher.
     *
     * @param mixed $expected
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
     * @param array ...$args
     *
     * @return \Mockery\Matcher\Ducktype
     */
    public static function ducktype(...$args)
    {
        return new \Mockery\Matcher\Ducktype($args);
    }

    /**
     * Return instance of SUBSET matcher.
     *
     * @param array $part
     * @param bool $strict - (Optional) True for strict comparison, false for loose
     *
     * @return \Mockery\Matcher\Subset
     */
    public static function subset(array $part, $strict = true)
    {
        return new \Mockery\Matcher\Subset($part, $strict);
    }

    /**
     * Return instance of CONTAINS matcher.
     *
     * @param mixed $args
     *
     * @return \Mockery\Matcher\Contains
     */
    public static function contains(...$args)
    {
        return new \Mockery\Matcher\Contains($args);
    }

    /**
     * Return instance of HASKEY matcher.
     *
     * @param mixed $key
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
     * @param mixed $val
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
     * @param $reference
     *
     * @return \Mockery\Matcher\Closure
     */
    public static function capture(&$reference)
    {
        $closure = function ($argument) use (&$reference) {
            $reference = $argument;
            return true;
        };

        return new \Mockery\Matcher\Closure($closure);
    }

    /**
     * Return instance of CLOSURE matcher.
     *
     * @param mixed $closure
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
     * @param mixed $expected
     *
     * @return \Mockery\Matcher\MustBe
     */
    public static function mustBe($expected)
    {
        return new \Mockery\Matcher\MustBe($expected);
    }

    /**
     * Return instance of IsEqual matcher.
     *
     * @template TExpected
     * @param TExpected $expected
     */
    public static function isEqual($expected): IsEqual
    {
        return new IsEqual($expected);
    }

    /**
     * Return instance of IsSame matcher.
     *
     * @template TExpected
     * @param TExpected $expected
     */
    public static function isSame($expected): IsSame
    {
        return new IsSame($expected);
    }

    /**
     * Return instance of NOT matcher.
     *
     * @param mixed $expected
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
     * @param array ...$args
     *
     * @return \Mockery\Matcher\AnyOf
     */
    public static function anyOf(...$args)
    {
        return new \Mockery\Matcher\AnyOf($args);
    }

    /**
     * Return instance of NOTANYOF matcher.
     *
     * @param array ...$args
     *
     * @return \Mockery\Matcher\NotAnyOf
     */
    public static function notAnyOf(...$args)
    {
        return new \Mockery\Matcher\NotAnyOf($args);
    }

    /**
     * Return instance of PATTERN matcher.
     *
     * @param mixed $expected
     *
     * @return \Mockery\Matcher\Pattern
     */
    public static function pattern($expected)
    {
        return new \Mockery\Matcher\Pattern($expected);
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
     * @param mixed $argument
     * @param int $depth
     *
     * @return mixed
     */
    private static function formatArgument($argument, $depth = 0)
    {
        if ($argument instanceof MatcherInterface) {
            return (string) $argument;
        }

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

                $argument = "[" . implode(", ", $sample) . "]";
            }

            return ((strlen($argument) > 1000) ? substr($argument, 0, 1000) . '...]' : $argument);
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

        return "'" . (string) $argument . "'";
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
     * @param object $object
     * @param int $nesting
     *
     * @return array
     */
    private static function objectToArray($object, $nesting = 3)
    {
        if ($nesting == 0) {
            return array('...');
        }

        $defaultFormatter = function ($object, $nesting) {
            return array('properties' => self::extractInstancePublicProperties($object, $nesting));
        };

        $class = get_class($object);

        $formatter = self::getConfiguration()->getObjectFormatter($class, $defaultFormatter);

        $array = array(
          'class' => $class,
          'identity' => '#' . md5(spl_object_hash($object))
        );

        $array = array_merge($array, $formatter($object, $nesting));

        return $array;
    }

    /**
     * Returns all public instance properties.
     *
     * @param mixed $object
     * @param int $nesting
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
                try {
                    $cleanedProperties[$name] = self::cleanupNesting($object->$name, $nesting);
                } catch (\Exception $exception) {
                    $cleanedProperties[$name] = $exception->getMessage();
                }
            }
        }

        return $cleanedProperties;
    }

    /**
     * Utility method used for recursively generating
     * an object or array representation.
     *
     * @param mixed $argument
     * @param int $nesting
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
     * @param Mockery\LegacyMockInterface $mock
     * @param array ...$args
     * @param callable $add
     * @return \Mockery\CompositeExpectation
     */
    public static function parseShouldReturnArgs(\Mockery\LegacyMockInterface $mock, $args, $add)
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
     * @param \Mockery\LegacyMockInterface $mock
     * @param string $arg
     * @param callable $add
     * @throws Mockery\Exception
     * @return \Mockery\ExpectationInterface
     */
    protected static function buildDemeterChain(\Mockery\LegacyMockInterface $mock, $arg, $add)
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

        $parent = get_class($mock);

        while (true) {
            $method = array_shift($methodNames);
            $expectations = $mock->mockery_getExpectationsFor($method);

            if (is_null($expectations) || self::noMoreElementsInChain($methodNames)) {
                $expectations = $nextExp($method);
                if (self::noMoreElementsInChain($methodNames)) {
                    break;
                }

                $mock = self::getNewDemeterMock($container, $parent, $method, $expectations);
            } else {
                $demeterMockKey = $container->getKeyOfDemeterMockFor($method, $parent);
                if ($demeterMockKey) {
                    $mock = self::getExistingDemeterMock($container, $demeterMockKey);
                }
            }

            $parent .= '->' . $method;

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
     * @param string $parent
     * @param string $method
     * @param Mockery\ExpectationInterface $exp
     *
     * @return \Mockery\Mock
     */
    private static function getNewDemeterMock(
        Mockery\Container $container,
        $parent,
        $method,
        Mockery\ExpectationInterface $exp
    ) {
        $newMockName = 'demeter_' . md5($parent) . '_' . $method;

        $parRef = null;
        $parRefMethod = null;
        $parRefMethodRetType = null;

        $parentMock = $exp->getMock();
        if ($parentMock !== null) {
            $parRef = new ReflectionObject($parentMock);
        }

        if ($parRef !== null && $parRef->hasMethod($method)) {
            $parRefMethod = $parRef->getMethod($method);
            $parRefMethodRetType = Reflector::getReturnType($parRefMethod, true);

            if ($parRefMethodRetType !== null && $parRefMethodRetType !== 'mixed') {
                $nameBuilder = new MockNameBuilder();
                $nameBuilder->addPart('\\' . $newMockName);
                $mock = self::namedMock($nameBuilder->build(), $parRefMethodRetType);
                $exp->andReturn($mock);

                return $mock;
            }
        }

        $mock = $container->mock($newMockName);
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

    public static function declareClass($fqn)
    {
        return static::declareType($fqn, "class");
    }

    public static function declareInterface($fqn)
    {
        return static::declareType($fqn, "interface");
    }

    private static function declareType($fqn, $type)
    {
        $targetCode = "<?php ";
        $shortName = $fqn;

        if (strpos($fqn, "\\")) {
            $parts = explode("\\", $fqn);

            $shortName = trim(array_pop($parts));
            $namespace = implode("\\", $parts);

            $targetCode.= "namespace $namespace;\n";
        }

        $targetCode.= "$type $shortName {} ";

        /*
         * We could eval here, but it doesn't play well with the way
         * PHPUnit tries to backup global state and the require definition
         * loader
         */
        $tmpfname = tempnam(sys_get_temp_dir(), "Mockery");
        file_put_contents($tmpfname, $targetCode);
        require $tmpfname;
        \Mockery::registerFileForCleanUp($tmpfname);
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
