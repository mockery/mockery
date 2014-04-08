<?php

namespace Mockery\Generator;

/**
 * This class describes the configuration of mocks and hides away some of the
 * reflection implementation
 */
class MockConfiguration
{
    protected static $mockCounter = 0;

    /**
     * A class that we'd like to mock
     */
    protected $targetClass;
    protected $targetClassName;

    /**
     * A number of interfaces we'd like to mock, keyed by name to attempt to
     * keep unique
     */
    protected $targetInterfaces = array();
    protected $targetInterfaceNames = array();

    /**
     * An object we'd like our mock to proxy to
     */
    protected $targetObject;

    /**
     * The class name we'd like to use for a generated mock
     */
    protected $name;

    /**
     * Methods that should specifically not be mocked
     *
     * This is currently populated with stuff we don't know how to deal with,
     * should really be somewhere else
     */
    protected $blackListedMethods = array();

    /**
     * If not empty, only these methods will be mocked
     */
    protected $whiteListedMethods = array();

    /**
     * An instance mock is where we override the original class before it's
     * autoloaded
     */
    protected $instanceMock = false;

    /**
     * Param overrides
     */
    protected $parameterOverrides = array();

    /**
     * Instance cache of all methods
     */
    protected $allMethods;

    public function __construct(array $targets = array(), array $blackListedMethods = array(), array $whiteListedMethods = array(), $name = null, $instanceMock = false, array $parameterOverrides = array())
    {
        $this->addTargets($targets);
        $this->blackListedMethods = $blackListedMethods;
        $this->whiteListedMethods = $whiteListedMethods;
        $this->name = $name;
        $this->instanceMock = $instanceMock;
        $this->parameterOverrides = $parameterOverrides;
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
        $vars = array(
            'targetClassName' => $this->targetClassName,
            'targetInterfaceNames' => $this->targetInterfaceNames,
            'name' => $this->name,
            'blackListedMethods' => $this->blackListedMethods,
            'whiteListedMethod' => $this->whiteListedMethods,
            'instanceMock' => $this->instanceMock,
            'parameterOverrides' => $this->parameterOverrides,
        );

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
         * Whitelist trumps blacklist
         */
        if (count($this->getWhiteListedMethods())) {
            $whitelist = array_map('strtolower', $this->getWhiteListedMethods());
            $methods = array_filter($methods, function ($method) use ($whitelist) {
                return $method->isAbstract() || in_array(strtolower($method->getName()), $whitelist);
            });

            return $methods;
        }

        /**
         * Remove blacklisted methods
         */
        if (count($this->getBlackListedMethods())) {
            $blacklist = array_map('strtolower', $this->getBlackListedMethods());
            $methods = array_filter($methods, function ($method) use ($blacklist) {
                return !in_array(strtolower($method->getName()), $blacklist);
            });
        }

        return array_values($methods);
    }

    /**
     * We declare the __call method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     */
    public function requiresCallTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ("__call" === $method->getName()) {
                $params = $method->getParameters();
                return !$params[1]->isArray();
            }
        }

        return false;
    }

    /**
     * We declare the __callStatic method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     */
    public function requiresCallStaticTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ("__callStatic" === $method->getName()) {
                $params = $method->getParameters();
                return !$params[1]->isArray();
            }
        }

        return false;
    }

    public function rename($className)
    {
        $targets = array();

        if ($this->targetClassName) {
            $targets[] = $this->targetClassName;
        }

        if ($this->targetInterfaceNames) {
            $targets = array_merge($targets, $this->targetInterfaceNames);
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
            $this->parameterOverrides
        );
    }

    protected function addTarget($target)
    {
        if (is_object($target)) {
            $this->setTargetObject($target);
            $this->setTargetClassName(get_class($target));
            return $this;
        }

        if ($target[0] !== "\\") {
            $target = "\\" . $target;
        }

        if (class_exists($target)) {
            $this->setTargetClassName($target);
            return $this;
        }

        if (interface_exists($target)) {
            $this->addTargetInterfaceName($target);
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

    protected function addTargets($interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addTarget($interface);
        }
    }

    public function getTargetClassName()
    {
        return $this->targetClassName;
    }

    public function getTargetClass()
    {
        if ($this->targetClass) {
            return $this->targetClass;
        }

        if (!$this->targetClassName) {
            return null;
        }

        if (class_exists($this->targetClassName)) {
            $dtc = DefinedTargetClass::factory($this->targetClassName);

            if (!$this->getTargetObject() && $dtc->isFinal()) {
                throw new \Mockery\Exception(
                    'The class ' . $this->targetClassName . ' is marked final and its methods'
                    . ' cannot be replaced. Classes marked final can be passed in'
                    . ' to \Mockery::mock() as instantiated objects to create a'
                    . ' partial mock, but only if the mock is not subject to type'
                    . ' hinting checks.'
                );
            }

            $this->targetClass = $dtc;

        } else {
            $this->targetClass = new UndefinedTargetClass($this->targetClassName);
        }

        return $this->targetClass;
    }

    public function getTargetInterfaces()
    {
        if (!empty($this->targetInterfaces)) {
            return $this->targetInterfaces;
        }

        foreach ($this->targetInterfaceNames as $targetInterface) {
            if (!interface_exists($targetInterface)) {
                $this->targetInterfaces[] = new UndefinedTargetClass($targetInterface);
                return;
            }

            $dtc = DefinedTargetClass::factory($targetInterface);
            $extendedInterfaces = array_keys($dtc->getInterfaces());
            $extendedInterfaces[] = $targetInterface;

            $traversableFound = false;
            $iteratorShiftedToFront = false;
            foreach ($extendedInterfaces as $interface) {

                if (!$traversableFound && preg_match("/^\\?Iterator(|Aggregate)$/i", $interface)) {
                    break;
                }

                if (preg_match("/^\\\\?IteratorAggregate$/i", $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory("\\IteratorAggregate");
                    $iteratorShiftedToFront = true;
                } elseif (preg_match("/^\\\\?Iterator$/i", $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory("\\Iterator");
                    $iteratorShiftedToFront = true;
                } elseif (preg_match("/^\\\\?Traversable$/i", $interface)) {
                    $traversableFound = true;
                }
            }

            if ($traversableFound && !$iteratorShiftedToFront) {
                $this->targetInterfaces[] = DefinedTargetClass::factory("\\IteratorAggregate");
            }

            /**
             * We never straight up implement Traversable
             */
            if (!preg_match("/^\\\\?Traversable$/i", $targetInterface)) {
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

    public function getName()
    {
        return $this->name;
    }

    /**
     * Generate a suitable name based on the config
     */
    public function generateName()
    {
        $name = 'Mockery_' . static::$mockCounter++;

        if ($this->getTargetObject()) {
            $name .= "_" . str_replace("\\", "_", get_class($this->getTargetObject()));
        }

        if ($this->getTargetClass()) {
            $name .= "_" . str_replace("\\", "_", $this->getTargetClass()->getName());
        }

        if ($this->getTargetInterfaces()) {
            $name .= array_reduce($this->getTargetInterfaces(), function ($tmpname, $i) {
                $tmpname .= '_' . str_replace("\\", "_", $i->getName());
                return $tmpname;
            }, '');
        }

        return $name;
    }

    public function getShortName()
    {
        $parts = explode("\\", $this->getName());
        return array_pop($parts);
    }

    public function getNamespaceName()
    {
        $parts = explode("\\", $this->getName());
        array_pop($parts);

        if (count($parts)) {
            return implode("\\", $parts);
        }

        return "";
    }

    public function getBlackListedMethods()
    {
        return $this->blackListedMethods;
    }

    public function getWhiteListedMethods()
    {
        return $this->whiteListedMethods;
    }

    public function isInstanceMock()
    {
        return $this->instanceMock;
    }

    public function getParameterOverrides()
    {
        return $this->parameterOverrides;
    }

    protected function setTargetClassName($targetClassName)
    {
        $this->targetClassName = $targetClassName;
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

        $methods = array();
        foreach ($classes as $class) {
            $methods = array_merge($methods, $class->getMethods());
        }

        $names = array();
        $methods = array_filter($methods, function ($method) use (&$names) {
            if (in_array($method->getName(), $names)) {
                return false;
            }

            $names[] = $method->getName();
            return true;
        });

        return $this->allMethods = $methods;
    }

    /**
     * If we attempt to implement Traversable, we must ensure we are also
     * implementing either Iterator or IteratorAggregate, and that whichever one
     * it is comes before Traversable in the list of implements.
     */
    protected function addTargetInterfaceName($targetInterface)
    {
        $this->targetInterfaceNames[] = $targetInterface;
    }


    protected function setTargetObject($object)
    {
        $this->targetObject = $object;
    }

}
