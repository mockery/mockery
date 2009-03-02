<?php

class MockMe
{

    protected static $_mockedClasses = array();

    protected static $_mockedObjects = array();

    protected $_className = '';

    protected $_mockClassName = null;

    protected $_runkit = false;

    public function __construct($className, $customName = null)
    {
        $this->_className = $className;
        if (!is_null($customName)) {
            $this->setMockClassName($customName);
        }
    }

    public static function mock($className, $custom = null)
    {
        if (is_array($custom)) {
            $mockme = new self($className);
            $mockme->createStubObject();
        } else {
            $mockme = new self($className, $custom);
            $mockme->createMockObject();
        }
        if ($mockme->getMockClassName() === null) {
            $class = $mockme->getClassName();
        } else {
            $class = $mockme->getMockClassName();
        }
        $mockObject = new $class();
        if ($mockObject instanceof MockMe_Stub && is_array($custom)) {
            $mockObject->mockme_set($custom);
        } elseif (is_array($custom)) {
            // support stubbing existing classes
        }
        if (!in_array(get_class($mockObject), self::$_mockedClasses)
        && !$mockObject instanceof MockMe_Stub) {
            self::$_mockedClasses[] = get_class($mockObject);
            self::$_mockedObjects[] = $mockObject;
        }

        return $mockObject;
    }

    public static function verify()
    {
        $verified = true;
        foreach (self::$_mockedObjects as $mockObject) {
            if ($mockObject->mockme_verify() === false) {
                $verified = false;
                break;
            }
        }
        self::$_mockedObjects = array();
        return $verified;
    }

    public static function reset()
    {
        foreach (self::$_mockedClasses as $class) {
            MockMe_Mockery::reverseOn($class);
        }
        self::$_mockedClasses = array();
        self::$_mockedObjects = array();
    }

    public function getClassName()
    {
        return $this->_className;
    }

    public function setMockClassName($name = null)
    {
        if ($name === null) {
            $this->_mockClassName = 'MockMe_' . sha1(microtime());
        } else {
            $this->_mockClassName = $name;
        }
    }

    public function getMockClassName()
    {
        return $this->_mockClassName;
    }

    public function createMockObject()
    {
        if (!class_exists($this->getClassName(), true) && !interface_exists($this->getClassName(), true)) {
            $this->setMockClassName($this->getClassName());
        }
        if ($this->getClassName() == $this->getMockClassName()) {
            $definition = $this->_createStubDefinition();
            eval($definition);
        } else {
            $reflectedClass = new ReflectionClass($this->getClassName());
            if ($this->getMockClassName() === null) {
                if (interface_exists($this->getClassName(), true) ||
                $reflectedClass->isAbstract()) {
                    $this->setMockClassName();
                } else {
                    MockMe_Mockery::applyTo($this->getClassName(), $reflectedClass);
                    return;
                }
            }
            $definition = $this->_createReflectedDefinition($reflectedClass);
            eval($definition);
            MockMe_Mockery::applyTo($this->getMockClassName(), new ReflectionClass($this->getMockClassName()));
        }
    }

    public function createStubObject()
    {
        $definition = $this->_createStubDefinition();
        eval($definition);
    }

    protected function _createStubDefinition()
    {
        $definition = '';
        $definition .= 'class ' . $this->getClassName() .  ' extends MockMe_Stub {';
        $definition .= '}';
        return $definition;
    }

    protected function _createReflectedDefinition(ReflectionClass $reflectedClass)
    {
        $inheritance = '';
        $definition = '';
        if ($reflectedClass->isFinal()) {
            throw new MockMe_Exception('Unable to create a Test Double for a class marked final');
        }
        if ($reflectedClass->isInterface()) {
            $inheritance = ' implements ' . $this->getClassName();
        } else {
            $inheritance = ' extends ' . $this->getClassName();
        }
        $definition .= 'class ' . $this->getMockClassName() .  $inheritance . '{';
        $definition .= $this->_getImplementedMethodsDefinition($reflectedClass);
        $definition .= '}';
        return $definition;
    }

    protected function _getImplementedMethodsDefinition(ReflectionClass $reflectedClass)
    {
        $definition = '';
        $methods = $reflectedClass->getMethods();
        foreach ($methods as $method) {
            if ($method->isAbstract()) {
                $definition .= $this->_createMethodPrototypeDefinition($method);
                $definition .= '{';
                $definition .= '}';
            }
        }
        return $definition;
    }

    protected function _createMethodPrototypeDefinition(ReflectionMethod $method)
    {
        $definition = '';
        $methodParams = array();
        $params = $method->getParameters();
        foreach ($params as $param) {
            $paramDef = '';
            if ($param->isArray()) {
                $paramDef .= 'array ';
            } elseif ($param->getClass()) {
                $paramDef .= $param->getClass()->getName() . ' ';
            }
            $paramDef .= '$' . $param->getName();
            if ($param->isOptional()) {
                $paramDef .= ' = ';
                if ($param->isDefaultValueAvailable()) {
                    $paramDef .= var_export($param->getDefaultValue(), true);
                }
            }
            $methodParams[] = $paramDef;
        }
        $paramDef = implode(',', $methodParams);
        $modifiers = Reflection::getModifierNames($method->getModifiers());
        $access = str_replace('abstract ', '', implode(' ', $modifiers));
        $definition .= $access . ' function ' . $method->getName();
        $definition .= ' (' . $paramDef . ')';
        return $definition;
    }

}
