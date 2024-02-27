<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

use Mockery\ClosureWrapper;
use Mockery\CompositeExpectation;
use Mockery\Configuration;
use Mockery\Container;
use Mockery\ExpectationInterface;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\MockNameBuilder;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\LegacyMockInterface;
use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\Any;
use Mockery\Matcher\AnyOf;
use Mockery\Matcher\Closure as MockeryClosure;
use Mockery\Matcher\Contains;
use Mockery\Matcher\Ducktype;
use Mockery\Matcher\HasKey;
use Mockery\Matcher\HasValue;
use Mockery\Matcher\IsEqual;
use Mockery\Matcher\IsSame;
use Mockery\Matcher\MatcherInterface;
use Mockery\Matcher\MustBe;
use Mockery\Matcher\Not;
use Mockery\Matcher\NotAnyOf;
use Mockery\Matcher\Pattern;
use Mockery\Matcher\Subset;
use Mockery\Matcher\Type;
use Mockery\MockInterface;
use Mockery\Reflector;

class Mockery
{
    public const BLOCKS = 'Mockery_Forward_Blocks';

    /**
     * Global configuration handler containing configuration options.
     *
     * @var Configuration
     */
    protected static $_config = null;

    /**
     * Global container to hold all mocks for the current unit test running.
     *
     * @var Container|null
     */
    protected static $_container = null;

    /**
     * @var Generator
     */
    protected static $_generator;

    /**
     * @var Loader
     */
    protected static $_loader;

    /**
     * @var array
     */
    private static $_filesToCleanUp = [];

    /**
     * Return instance of AndAnyOtherArgs matcher.
     *
     * @return AndAnyOtherArgs
     */
    public static function andAnyOtherArgs()
    {
        return new AndAnyOtherArgs();
    }

    /**
     * Return instance of AndAnyOtherArgs matcher.
     *
     * An alternative name to `andAnyOtherArgs` so
     * the API stays closer to `any` as well.
     *
     * @return AndAnyOtherArgs
     */
    public static function andAnyOthers()
    {
        return new AndAnyOtherArgs();
    }

    /**
     * Return instance of ANY matcher.
     *
     * @return Any
     */
    public static function any()
    {
        return new Any();
    }

    /**
     * Return instance of ANYOF matcher.
     *
     * @param array ...$args
     *
     * @return AnyOf
     */
    public static function anyOf(...$args)
    {
        return new AnyOf($args);
    }

    /**
     * @return array
     *
     * @deprecated since 1.3.2 and will be removed in 2.0.
     */
    public static function builtInTypes()
    {
        return [
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
        ];
    }

    /**
     * Return instance of CLOSURE matcher.
     *
     * @return MockeryClosure
     */
    public static function capture(&$reference)
    {
        $closure = function ($argument) use (&$reference) {
            $reference = $argument;
            return true;
        };

        return new MockeryClosure($closure);
    }

    /**
     * Static shortcut to closing up and verifying all mocks in the global
     * container, and resetting the container static variable to null.
     */
    public static function close()
    {
        foreach (self::$_filesToCleanUp as $fileName) {
            @unlink($fileName);
        }
        self::$_filesToCleanUp = [];

        if (self::$_container === null) {
            return;
        }

        $container = self::$_container;
        self::$_container = null;

        $container->mockery_teardown();
        $container->mockery_close();
    }

    /**
     * Return instance of CONTAINS matcher.
     *
     * @param mixed $args
     *
     * @return Contains
     */
    public static function contains(...$args)
    {
        return new Contains($args);
    }

    public static function declareClass($fqn)
    {
        static::declareType($fqn, 'class');
    }

    public static function declareInterface($fqn)
    {
        static::declareType($fqn, 'interface');
    }

    /**
     * Return instance of DUCKTYPE matcher.
     *
     * @param array ...$args
     *
     * @return Ducktype
     */
    public static function ducktype(...$args)
    {
        return new Ducktype($args);
    }

    /**
     * Static fetching of a mock associated with a name or explicit class poser.
     *
     * @param string $name
     *
     * @return LegacyMockInterface|MockInterface
     */
    public static function fetchMock($name)
    {
        return self::getContainer()->fetchMock($name);
    }

    /**
     * Utility method to format method name and arguments into a string.
     *
     * @param string $method
     *
     * @return string
     */
    public static function formatArgs($method, array $arguments = null)
    {
        if ($arguments === null) {
            return $method . '()';
        }

        $formattedArguments = [];
        foreach ($arguments as $argument) {
            $formattedArguments[] = self::formatArgument($argument);
        }

        return $method . '(' . implode(', ', $formattedArguments) . ')';
    }

    /**
     * Utility function to format objects to printable arrays.
     *
     * @return string
     */
    public static function formatObjects(array $objects = null)
    {
        static $formatting;

        if ($formatting) {
            return '[Recursion]';
        }

        if ($objects === null) {
            return '';
        }

        $objects = array_filter($objects, 'is_object');
        if ($objects === []) {
            return '';
        }

        $parts = [];
        $formatting = true;

        foreach ($objects as $object) {
            $parts[get_class($object)] = self::objectToArray($object);
        }

        $formatting = false;

        return 'Objects: ( ' . var_export($parts, true) . ')';
    }

    /**
     * Lazy loader and Getter for the global
     * configuration container.
     *
     * @return Configuration
     */
    public static function getConfiguration()
    {
        if (self::$_config === null) {
            self::$_config = new Configuration();
        }

        return self::$_config;
    }

    /**
     * Lazy loader and getter for
     * the container property.
     *
     * @return Container
     */
    public static function getContainer()
    {
        if (self::$_container === null) {
            self::$_container = new Container(self::getGenerator(), self::getLoader());
        }

        return self::$_container;
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
     * Gets an EvalLoader to be used as default.
     *
     * @return EvalLoader
     */
    public static function getDefaultLoader()
    {
        return new EvalLoader();
    }

    /**
     * Lazy loader method and getter for
     * the generator property.
     *
     * @return Generator
     */
    public static function getGenerator()
    {
        if (self::$_generator === null) {
            self::$_generator = self::getDefaultGenerator();
        }

        return self::$_generator;
    }

    /**
     * Lazy loader method and getter for
     * the $_loader property.
     *
     * @return Loader
     */
    public static function getLoader()
    {
        if (self::$_loader === null) {
            self::$_loader = self::getDefaultLoader();
        }

        return self::$_loader;
    }

    /**
     * Defines the global helper functions
     */
    public static function globalHelpers()
    {
        require_once __DIR__ . '/helpers.php';
    }

    /**
     * Return instance of HASKEY matcher.
     *
     * @param mixed $key
     *
     * @return HasKey
     */
    public static function hasKey($key)
    {
        return new HasKey($key);
    }

    /**
     * Return instance of HASVALUE matcher.
     *
     * @param mixed $val
     *
     * @return HasValue
     */
    public static function hasValue($val)
    {
        return new HasValue($val);
    }

    /**
     * Static and Semantic shortcut to \Mockery\Container::mock().
     *
     * @param mixed ...$args
     *
     * @return MockInterface|LegacyMockInterface
     */
    public static function instanceMock(...$args)
    {
        return self::getContainer()->mock(...$args);
    }

    /**
     * @param  string $type
     * @return bool
     *
     * @deprecated since 1.3.2 and will be removed in 2.0.
     */
    public static function isBuiltInType($type)
    {
        return in_array($type, self::builtInTypes(), true);
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
     * Static shortcut to \Mockery\Container::mock().
     *
     * @param mixed ...$args
     *
     * @return MockInterface|LegacyMockInterface
     */
    public static function mock(...$args)
    {
        return self::getContainer()->mock(...$args);
    }

    /**
     * Return instance of MUSTBE matcher.
     *
     * @param mixed $expected
     *
     * @return MustBe
     */
    public static function mustBe($expected)
    {
        return new MustBe($expected);
    }

    /**
     * Static shortcut to \Mockery\Container::mock(), first argument names the mock.
     *
     * @param mixed ...$args
     *
     * @return MockInterface|LegacyMockInterface
     */
    public static function namedMock(...$args)
    {
        $name = array_shift($args);

        $builder = new MockConfigurationBuilder();
        $builder->setName($name);

        array_unshift($args, $builder);

        return self::getContainer()->mock(...$args);
    }

    /**
     * Return instance of NOT matcher.
     *
     * @param mixed $expected
     *
     * @return Not
     */
    public static function not($expected)
    {
        return new Not($expected);
    }

    /**
     * Return instance of NOTANYOF matcher.
     *
     * @param array ...$args
     *
     * @return NotAnyOf
     */
    public static function notAnyOf(...$args)
    {
        return new NotAnyOf($args);
    }

    /**
     * Return instance of CLOSURE matcher.
     *
     * @param Closure $closure
     *
     * @return MockeryClosure
     */
    public static function on($closure)
    {
        return new MockeryClosure($closure);
    }

    /**
     * Utility function to parse shouldReceive() arguments and generate
     * expectations from such as needed.
     *
     * @param  array                ...$args
     * @param  callable             $add
     * @return CompositeExpectation
     */
    public static function parseShouldReturnArgs(LegacyMockInterface $mock, $args, $add)
    {
        $composite = new CompositeExpectation();

        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $k => $v) {
                    $expectation = self::buildDemeterChain($mock, $k, $add)->andReturn($v);
                    $composite->add($expectation);
                }

                continue;
            }

            if (is_string($arg)) {
                $expectation = self::buildDemeterChain($mock, $arg, $add);
                $composite->add($expectation);
            }
        }

        return $composite;
    }

    /**
     * Return instance of PATTERN matcher.
     *
     * @param mixed $expected
     *
     * @return Pattern
     */
    public static function pattern($expected)
    {
        return new Pattern($expected);
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

    /**
     * Reset the container to null.
     */
    public static function resetContainer()
    {
        self::$_container = null;
    }

    /**
     * Static shortcut to \Mockery\Container::self().
     *
     * @return MockInterface|LegacyMockInterface
     * @throws LogicException
     */
    public static function self()
    {
        if (self::$_container === null) {
            throw new LogicException('You have not declared any mocks yet');
        }

        return self::$_container->self();
    }

    /**
     * Set the container.
     *
     * @return Container
     */
    public static function setContainer(Container $container)
    {
        return self::$_container = $container;
    }

    /**
     * Setter for the $_generator static property.
     */
    public static function setGenerator(Generator $generator)
    {
        self::$_generator = $generator;
    }

    /**
     * Setter for the $_loader static property.
     */
    public static function setLoader(Loader $loader)
    {
        self::$_loader = $loader;
    }

    /**
     * Static and semantic shortcut for getting a mock from the container
     * and applying the spy's expected behavior into it.
     *
     * @param mixed ...$args
     *
     * @return MockInterface|LegacyMockInterface
     */
    public static function spy(...$args)
    {
        if (count($args) && $args[0] instanceof Closure) {
            $args[0] = new ClosureWrapper($args[0]);
        }

        return self::getContainer()->mock(...$args)->shouldIgnoreMissing();
    }

    /**
     * Return instance of SUBSET matcher.
     *
     * @param bool $strict - (Optional) True for strict comparison, false for loose
     *
     * @return Subset
     */
    public static function subset(array $part, $strict = true)
    {
        return new Subset($part, $strict);
    }

    /**
     * Return instance of TYPE matcher.
     *
     * @param mixed $expected
     *
     * @return Type
     */
    public static function type($expected)
    {
        return new Type($expected);
    }

    /**
     * Sets up expectations on the members of the CompositeExpectation and
     * builds up any demeter chain that was passed to shouldReceive.
     *
     * @param  string               $arg
     * @param  callable             $add
     * @return ExpectationInterface
     * @throws Mockery\Exception
     */
    protected static function buildDemeterChain(LegacyMockInterface $mock, $arg, $add)
    {
        /** @var Container $container */
        $container = $mock->mockery_getContainer();

        $methodNames = explode('->', $arg);

        reset($methodNames);

        $methodName = current($methodNames);

        if (
            ! $mock->mockery_isAnonymous()
            && ! self::getConfiguration()->mockingNonExistentMethodsAllowed()
            && ! in_array($methodName, $mock->mockery_getMockableMethods(), true)
        ) {
            throw new \Mockery\Exception(
                sprintf(
                    "Mockery's configuration currently forbids mocking the method %s as it does not exist on the class or object being mocked",
                    $methodName
                )
            );
        }

        /** @var callable(string):mixed $nextExp */
        $nextExp = static function (string $method) use ($add) {
            return $add($method);
        };

        $parent = get_class($mock);

        while (true) {
            $method = array_shift($methodNames);
            $expectations = $mock->mockery_getExpectationsFor($method);
            $noMoreElementsInChain = self::noMoreElementsInChain($methodNames);

            if ($expectations === null || $noMoreElementsInChain) {
                $expectations = $nextExp($method);
                if ($noMoreElementsInChain) {
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

            $nextExp = static function (string $method) use ($mock) {
                return $mock->allows($method);
            };
        }

        return $expectations;
    }

    /**
     * Utility method for recursively generating a representation of the given array.
     *
     * @return mixed
     */
    private static function cleanupArray(array $argument, int $nesting = 3)
    {
        if ($nesting === 0) {
            return '...';
        }

        foreach ($argument as $key => $value) {
            if (is_array($value)) {
                $argument[$key] = self::cleanupArray($value, $nesting - 1);

                continue;
            }

            if (is_object($value)) {
                $argument[$key] = self::objectToArray($value, $nesting - 1);
            }
        }

        return $argument;
    }

    /**
     * Utility method used for recursively generating an object or array representation.
     *
     * @param mixed $argument
     *
     * @return mixed
     */
    private static function cleanupNesting($argument, int $nesting)
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

    private static function declareType(string $fqn, string $type): void
    {
        $targetCode = '<?php ';
        $shortName = $fqn;

        if (strpos($fqn, '\\')) {
            $parts = explode('\\', $fqn);

            $shortName = trim(array_pop($parts));
            $namespace = implode('\\', $parts);

            $targetCode .= "namespace {$namespace};\n";
        }

        $targetCode .= $type . ' ' . $shortName . ' {} ';

        /*
         * We could eval here, but it doesn't play well with the way
         * PHPUnit tries to backup global state and the require definition
         * loader
         */
        $filename = tempnam(sys_get_temp_dir(), 'Mockery');

        file_put_contents($filename, $targetCode);

        require $filename;

        self::registerFileForCleanUp($filename);
    }

    /**
     * Returns all public instance properties.
     *
     * @param mixed $object
     */
    private static function extractInstancePublicProperties(object $object, int $nesting): array
    {
        $reflection = new ReflectionClass(get_class($object));
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $cleanedProperties = [];

        foreach ($properties as $publicProperty) {
            if (! $publicProperty->isStatic()) {
                $name = $publicProperty->getName();

                try {
                    $cleanedProperties[$name] = self::cleanupNesting($object->{$name}, $nesting);
                } catch (Exception $exception) {
                    $cleanedProperties[$name] = $exception->getMessage();
                }
            }
        }

        return $cleanedProperties;
    }

    /**
     * Gets the string representation
     * of any passed argument.
     *
     * @param mixed $argument
     *
     * @return mixed
     */
    private static function formatArgument($argument, int $depth = 0)
    {
        if ($argument instanceof MatcherInterface) {
            return (string) $argument;
        }

        if ($argument === null) {
            return 'NULL';
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
                $sample = [];
                foreach ($argument as $key => $value) {
                    $key = is_int($key) ? $key : "'{$key}'";
                    $value = self::formatArgument($value, $depth + 1);
                    $sample[] = "{$key} => {$value}";
                }

                $argument = '[' . implode(', ', $sample) . ']';
            }

            return (strlen($argument) > 1000) ? substr($argument, 0, 1000) . '...]' : $argument;
        }

        if (is_bool($argument)) {
            return $argument ? 'true' : 'false';
        }

        if (is_resource($argument)) {
            return 'resource(...)';
        }

        return "'" . $argument . "'";
    }

    /**
     * Gets a specific demeter mock from
     * the ones kept by the container.
     *
     * @return LegacyMockInterface|MockInterface|null
     */
    private static function getExistingDemeterMock(
        Container $container,
        string $demeterMockKey
    ) {
        return $container->getMocks()[$demeterMockKey] ?? null;
    }

    /**
     * Gets a new demeter configured
     * mock from the container.
     *
     * @return MockInterface|LegacyMockInterface
     */
    private static function getNewDemeterMock(
        Container $container,
        string $parent,
        string $method,
        ExpectationInterface $expectation
    ) {
        $newMockName = 'demeter_' . md5($parent) . '_' . $method;

        $parentMock = $expectation->getMock();
        if ($parentMock !== null) {
            $parentObject = new ReflectionObject($parentMock);
            if ($parentObject->hasMethod($method)) {
                $returnType = Reflector::getReturnType(
                    $parentObject->getMethod($method),
                    true
                );

                if ($returnType !== null && $returnType !== 'mixed') {
                    $nameBuilder = new MockNameBuilder();

                    $name = $nameBuilder->addPart('\\' . $newMockName)->build();

                    $mock = self::namedMock($name, $returnType);

                    $expectation->andReturn($mock);

                    return $mock;
                }
            }
        }

        $mock = $container->mock($newMockName);
        $expectation->andReturn($mock);
        return $mock;
    }

    /**
     * Checks if the passed array representing a demeter
     * chain with the method names is empty.
     */
    private static function noMoreElementsInChain(array $methodNames): bool
    {
        return $methodNames === [];
    }

    /**
     * Utility function to turn public properties and public get* and is* method values into an array.
     */
    private static function objectToArray(object $object, int $nesting = 3): array
    {
        if ($nesting === 0) {
            return ['...'];
        }

        $defaultFormatter = static function (object $object, int $nesting) {
            return [
                'properties' => self::extractInstancePublicProperties($object, $nesting),
            ];
        };

        $class = get_class($object);

        $formatter = self::getConfiguration()->getObjectFormatter($class, $defaultFormatter);

        return array_merge(
            [
                'class' => $class,
                'identity' => '#' . md5(spl_object_hash($object)),
            ],
            $formatter($object, $nesting)
        );
    }
}
