<?php

class MockMe
{

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
        $reflectedClass = new ReflectionClass($class);
        $mockObject = $reflectedClass->newInstance();
        if ($mockObject instanceof MockMe_Stub && is_array($custom)) {
            $mockObject->mockme_set($custom);
        }
        return $mockObject;
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
        } else {
            $reflectedClass = new ReflectionClass($this->getClassName());
            if ($this->getMockClassName() === null) {
                if (interface_exists($this->getClassName(), true) ||
                $reflectedClass->isAbstract()) {
                    $this->setMockClassName();
                } else {
                    return;
                }
            }
            $definition = $this->_createReflectedDefinition($reflectedClass);
        }
        eval($definition);
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

/**$reflectedClass = new ReflectionClass($mockme->getMockClassName());

        $constructor = null;
        if ($reflectedClass->hasMethod('__construct')) {
            $constructor = '__construct';
        } elseif ($reflectedClass->hasMethod($mockme->getMockClassName())) {
            $constructor = $mockme->getMockClassName();
        }

        $mockObject = null;
        if (!is_null($constructor)) {
            $constructMethod = $reflectedClass->getMethod($constructor);
            $constructParams = $constructMethod->getParameters();
            if (count($constructParams) == 0) {
                $mockObject = $reflectedClass->newInstance();
            } else {
                $params = array();
                foreach ($constructParams as $param) {
                    if ($param->isOptional()) {
                        $params[] = null;
                        continue;
                    }
                    if ($param->isArray()) {
                        $params[] = array();
                        continue;
                    }
                    $classHint = $param->getClass();
                    if ($classHint) {
                        $params[] = $classHint->newInstance();
                        continue;
                    }
                    $params[] = null;
                }
                $mockObject = $reflectedClass->newInstanceArgs($params);
            }
        } else {
            $mockObject = $reflectedClass->newInstance();
        }*/


/**if (!defined('RUNKIT_ACC_STATIC')) {
            throw new MockMe_Exception('Detected runkit extension is not sufficient; refer to MockMe documentation for a working replacement module');
        }**/
