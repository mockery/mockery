<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Exception;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_unique;
use function array_values;
use function class_alias;
use function class_exists;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function interface_exists;
use function is_object;
use function md5;
use function preg_match;
use function serialize;
use function strpos;
use function strtolower;
use function trait_exists;

/**
 * This class describes the configuration of mocks and hides away some of the
 * reflection implementation
 */
class MockConfiguration
{
    /**
     * Instance cache of all methods
     */
    protected $allMethods;

    /**
     * Methods that should specifically not be mocked
     *
     * This is currently populated with stuff we don't know how to deal with,
     * should really be somewhere else
     */
    protected $blackListedMethods = [];

    protected $constantsMap = [];

    /**
     * An instance mock is where we override the original class before it's
     * autoloaded
     */
    protected $instanceMock = false;

    /**
     * If true, overrides original class destructor
     */
    protected $mockOriginalDestructor = false;

    /**
     * The class name we'd like to use for a generated mock
     */
    protected $name;

    /**
     * Param overrides
     */
    protected $parameterOverrides = [];

    /**
     * A class that we'd like to mock
     */
    protected $targetClass;

    protected $targetClassName;

    protected $targetInterfaceNames = [];

    /**
     * A number of interfaces we'd like to mock, keyed by name to attempt to
     * keep unique
     */
    protected $targetInterfaces = [];

    /**
     * An object we'd like our mock to proxy to
     */
    protected $targetObject;

    protected $targetTraitNames = [];

    /**
     * A number of traits we'd like to mock, keyed by name to attempt to
     * keep unique
     */
    protected $targetTraits = [];

    /**
     * If not empty, only these methods will be mocked
     */
    protected $whiteListedMethods = [];

    public function __construct(
        array $targets = [],
        array $blackListedMethods = [],
        array $whiteListedMethods = [],
        $name = null,
        $instanceMock = false,
        array $parameterOverrides = [],
        $mockOriginalDestructor = false,
        array $constantsMap = []
    ) {
        $this->addTargets($targets);
        $this->blackListedMethods = $blackListedMethods;
        $this->whiteListedMethods = $whiteListedMethods;
        $this->name = $name;
        $this->instanceMock = $instanceMock;
        $this->parameterOverrides = $parameterOverrides;
        $this->mockOriginalDestructor = $mockOriginalDestructor;
        $this->constantsMap = $constantsMap;
    }

    /**
     * Generate a suitable name based on the config
     */
    public function generateName()
    {
        $nameBuilder = new MockNameBuilder();

        if ($this->getTargetObject()) {
            $className = get_class($this->getTargetObject());
            $nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
        }

        if ($this->getTargetClass()) {
            $className = $this->getTargetClass()->getName();
            $nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
        }

        foreach ($this->getTargetInterfaces() as $targetInterface) {
            $nameBuilder->addPart($targetInterface->getName());
        }

        return $nameBuilder->build();
    }

    public function getBlackListedMethods()
    {
        return $this->blackListedMethods;
    }

    public function getConstantsMap()
    {
        return $this->constantsMap;
    }

    /**
     * Attempt to create a hash of the configuration, in order to allow caching
     *
     * @TODO workout if this will work
     *
     * @return string
     */
    public function getHash()
    {
        $vars = [
            'targetClassName'        => $this->targetClassName,
            'targetInterfaceNames'   => $this->targetInterfaceNames,
            'targetTraitNames'       => $this->targetTraitNames,
            'name'                   => $this->name,
            'blackListedMethods'     => $this->blackListedMethods,
            'whiteListedMethod'      => $this->whiteListedMethods,
            'instanceMock'           => $this->instanceMock,
            'parameterOverrides'     => $this->parameterOverrides,
            'mockOriginalDestructor' => $this->mockOriginalDestructor,
        ];

        return md5(serialize($vars));
    }

    /**
     * Gets a list of methods from the classes, interfaces and objects and
     * filters them appropriately. Lot's of filtering going on, perhaps we could
     * have filter classes to iterate through
     */
    public function getMethodsToMock()
    {
        $methods = $this->getAllMethods();

        foreach ($methods as $key => $method) {
            if ($method->isFinal()) {
                unset($methods[$key]);
            }
        }

        /**
         * Whitelist trumps everything else
         */
        if ($this->getWhiteListedMethods() !== []) {
            $whitelist = array_map('strtolower', $this->getWhiteListedMethods());

            return array_filter($methods, static function ($method) use ($whitelist) {
                if ($method->isAbstract()) {
                    return true;
                }

                return in_array(strtolower($method->getName()), $whitelist, true);
            });
        }

        /**
         * Remove blacklisted methods
         */
        if ($this->getBlackListedMethods() !== []) {
            $blacklist = array_map('strtolower', $this->getBlackListedMethods());
            $methods = array_filter($methods, static function ($method) use ($blacklist) {
                return ! in_array(strtolower($method->getName()), $blacklist, true);
            });
        }

        /**
         * Internal objects can not be instantiated with newInstanceArgs and if
         * they implement Serializable, unserialize will have to be called. As
         * such, we can't mock it and will need a pass to add a dummy
         * implementation
         */
        if ($this->getTargetClass()
            && $this->getTargetClass()->implementsInterface('Serializable')
            && $this->getTargetClass()->hasInternalAncestor()) {
            $methods = array_filter($methods, static function ($method) {
                return $method->getName() !== 'unserialize';
            });
        }

        return array_values($methods);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespaceName()
    {
        $parts = explode('\\', $this->getName());
        array_pop($parts);

        if ($parts !== []) {
            return implode('\\', $parts);
        }

        return '';
    }

    public function getParameterOverrides()
    {
        return $this->parameterOverrides;
    }

    public function getShortName()
    {
        $parts = explode('\\', $this->getName());
        return array_pop($parts);
    }

    public function getTargetClass()
    {
        if ($this->targetClass) {
            return $this->targetClass;
        }

        if (! $this->targetClassName) {
            return null;
        }

        if (class_exists($this->targetClassName)) {
            $alias = null;
            if (strpos($this->targetClassName, '@') !== false) {
                $alias = (new MockNameBuilder())
                    ->addPart('anonymous_class')
                    ->addPart(md5($this->targetClassName))
                    ->build();
                class_alias($this->targetClassName, $alias);
            }

            $dtc = DefinedTargetClass::factory($this->targetClassName, $alias);

            if ($this->getTargetObject() === null && $dtc->isFinal()) {
                throw new Exception(
                    'The class ' . $this->targetClassName . ' is marked final and its methods'
                    . ' cannot be replaced. Classes marked final can be passed in'
                    . ' to \Mockery::mock() as instantiated objects to create a'
                    . ' partial mock, but only if the mock is not subject to type'
                    . ' hinting checks.'
                );
            }

            $this->targetClass = $dtc;
        } else {
            $this->targetClass = UndefinedTargetClass::factory($this->targetClassName);
        }

        return $this->targetClass;
    }

    public function getTargetClassName()
    {
        return $this->targetClassName;
    }

    public function getTargetInterfaces()
    {
        if ($this->targetInterfaces !== []) {
            return $this->targetInterfaces;
        }

        foreach ($this->targetInterfaceNames as $targetInterface) {
            if (! interface_exists($targetInterface)) {
                $this->targetInterfaces[] = UndefinedTargetClass::factory($targetInterface);
                continue;
            }

            $dtc = DefinedTargetClass::factory($targetInterface);
            $extendedInterfaces = array_keys($dtc->getInterfaces());
            $extendedInterfaces[] = $targetInterface;

            $traversableFound = false;
            $iteratorShiftedToFront = false;
            foreach ($extendedInterfaces as $interface) {
                if (! $traversableFound && preg_match('/^\\?Iterator(|Aggregate)$/i', $interface)) {
                    break;
                }

                if (preg_match('/^\\\\?IteratorAggregate$/i', $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
                    $iteratorShiftedToFront = true;

                    continue;
                }

                if (preg_match('/^\\\\?Iterator$/i', $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory('\\Iterator');
                    $iteratorShiftedToFront = true;

                    continue;
                }

                if (preg_match('/^\\\\?Traversable$/i', $interface)) {
                    $traversableFound = true;
                }
            }

            if ($traversableFound && ! $iteratorShiftedToFront) {
                $this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
            }

            /**
             * We never straight up implement Traversable
             */
            $isTraversable = preg_match('/^\\\\?Traversable$/i', $targetInterface);
            if ($isTraversable === 0 || $isTraversable === false) {
                $this->targetInterfaces[] = $dtc;
            }
        }

        $this->targetInterfaces = array_unique($this->targetInterfaces); // just in case
        return $this->targetInterfaces;
    }

    public function getTargetObject()
    {
        return $this->targetObject;
    }

    public function getTargetTraits()
    {
        if ($this->targetTraits !== []) {
            return $this->targetTraits;
        }

        foreach ($this->targetTraitNames as $targetTrait) {
            $this->targetTraits[] = DefinedTargetClass::factory($targetTrait);
        }

        $this->targetTraits = array_unique($this->targetTraits); // just in case
        return $this->targetTraits;
    }

    public function getWhiteListedMethods()
    {
        return $this->whiteListedMethods;
    }

    public function isInstanceMock()
    {
        return $this->instanceMock;
    }

    public function isMockOriginalDestructor()
    {
        return $this->mockOriginalDestructor;
    }

    public function rename($className)
    {
        $targets = [];

        if ($this->targetClassName) {
            $targets[] = $this->targetClassName;
        }

        if ($this->targetInterfaceNames) {
            $targets = array_merge($targets, $this->targetInterfaceNames);
        }

        if ($this->targetTraitNames) {
            $targets = array_merge($targets, $this->targetTraitNames);
        }

        if ($this->targetObject) {
            $targets[] = $this->targetObject;
        }

        return new self(
            $targets,
            $this->blackListedMethods,
            $this->whiteListedMethods,
            $className,
            $this->instanceMock,
            $this->parameterOverrides,
            $this->mockOriginalDestructor,
            $this->constantsMap
        );
    }

    /**
     * We declare the __callStatic method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     */
    public function requiresCallStaticTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ($method->getName() === '__callStatic') {
                $params = $method->getParameters();
                return ! $params[1]->isArray();
            }
        }

        return false;
    }

    /**
     * We declare the __call method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     */
    public function requiresCallTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ($method->getName() === '__call') {
                $params = $method->getParameters();
                return ! $params[1]->isArray();
            }
        }

        return false;
    }

    protected function addTarget($target)
    {
        if (is_object($target)) {
            $this->setTargetObject($target);
            $this->setTargetClassName(get_class($target));
            return $this;
        }

        if ($target[0] !== '\\') {
            $target = '\\' . $target;
        }

        if (class_exists($target)) {
            $this->setTargetClassName($target);
            return $this;
        }

        if (interface_exists($target)) {
            $this->addTargetInterfaceName($target);
            return $this;
        }

        if (trait_exists($target)) {
            $this->addTargetTraitName($target);
            return $this;
        }

        /**
         * Default is to set as class, or interface if class already set
         *
         * Don't like this condition, can't remember what the default
         * targetClass is for
         */
        if ($this->getTargetClassName()) {
            $this->addTargetInterfaceName($target);
            return $this;
        }

        $this->setTargetClassName($target);
    }

    /**
     * If we attempt to implement Traversable, we must ensure we are also
     * implementing either Iterator or IteratorAggregate, and that whichever one
     * it is comes before Traversable in the list of implements.
     *
     * @param mixed $targetInterface
     */
    protected function addTargetInterfaceName($targetInterface)
    {
        $this->targetInterfaceNames[] = $targetInterface;
    }

    protected function addTargetTraitName($targetTraitName)
    {
        $this->targetTraitNames[] = $targetTraitName;
    }

    protected function addTargets($interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addTarget($interface);
        }
    }

    protected function getAllMethods()
    {
        if ($this->allMethods) {
            return $this->allMethods;
        }

        $classes = $this->getTargetInterfaces();

        if ($this->getTargetClass()) {
            $classes[] = $this->getTargetClass();
        }

        $methods = [];
        foreach ($classes as $class) {
            $methods = array_merge($methods, $class->getMethods());
        }

        foreach ($this->getTargetTraits() as $trait) {
            foreach ($trait->getMethods() as $method) {
                if ($method->isAbstract()) {
                    $methods[] = $method;
                }
            }
        }

        $names = [];
        $methods = array_filter($methods, static function ($method) use (&$names) {
            if (in_array($method->getName(), $names, true)) {
                return false;
            }

            $names[] = $method->getName();
            return true;
        });

        return $this->allMethods = $methods;
    }

    protected function setTargetClassName($targetClassName)
    {
        $this->targetClassName = $targetClassName;
    }

    protected function setTargetObject($object)
    {
        $this->targetObject = $object;
    }
}
