<?php

class Mockery
{

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

    public static function mock($className, $custom = null, array $ctorArguments = array())
    {
        if (is_array($custom) && !class_exists($className)) {
            $mockery = new self($className);
            $mockery->createStubObject();
        } else {
            if (is_array($custom)) {
                $mockery = new self($className, null);
            } else {
                $mockery = new self($className, $custom);
            }
            $mockery->createMockObject();
        }
        if ($mockery->getMockClassName() === null) {
            $class = $mockery->getClassName();
        } else {
            $class = $mockery->getMockClassName();
        }
        if (count($ctorArguments) > 0)
        {
        	$refMockObject = new ReflectionClass($class);
        	$ctorArguments = self::_orderArguments($ctorArguments, $refMockObject);
        	$mockObject = $refMockObject->newInstanceArgs($ctorArguments);
        }
        else
        {
        	$mockObject = new $class();
        }
        if ($mockObject instanceof Mockery_Stub && is_array($custom)) {
            $mockObject->mockery_set($custom);
        } elseif (is_array($custom)) {
            foreach ($custom as $method => $return) {
                $mockObject->shouldReceive($method)
                    ->withAnyArgs()
                    ->zeroOrMoreTimes()
                    ->andReturn($return);
            }
        }
        if (!$mockObject instanceof Mockery_Stub) {
            self::$_mockedObjects[] = $mockObject;
        }

        return $mockObject;
    }

    public static function verify()
    {
        $verified = true;
        foreach (self::$_mockedObjects as $mockObject) {
            if ($mockObject->mockery_verify() === false) {
                $verified = false;
                break;
            }
        }
        self::$_mockedObjects = array();
        return $verified;
    }

    public function getClassName()
    {
        return $this->_className;
    }

    public function setMockClassName($name = null)
    {
        if ($name === null) {
            $this->_mockClassName = uniqid('Mockery_');
        } else {
            $this->_mockClassName = $name;
        }
    }

    public function getMockClassName()
    {
        return $this->_mockClassName;
    }

    public function createMockObject(array $ctorArguments = array())
    {
        if (!class_exists($this->getClassName(), true) && !interface_exists($this->getClassName(), true)) {
            $this->setMockClassName($this->getClassName());
        }
        if ($this->getClassName() == $this->getMockClassName()) {
            $definition = $this->_createClassDefinition($this->getClassName());
            eval($definition);
        } else {
            $reflectedClass = new ReflectionClass($this->getClassName());
            if ($this->getMockClassName() === null) {
                $this->setMockClassName();
            }
            $definition = $this->_createReflectedDefinition($reflectedClass);
            eval($definition);
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
        $definition .= 'class ' . $this->getClassName() .  ' extends Mockery_Stub {';
        $definition .= '}';
        return $definition;
    }

    protected function _createReflectedDefinition(ReflectionClass $reflectedClass)
    {
        $inheritance = '';
        $definition = '';
        if ($reflectedClass->isFinal()) {
            throw new Mockery_Exception('Unable to create a Test Double for a class marked final');
        }
        if ($reflectedClass->isInterface()) {
            $inheritance = ' implements ' . $this->getClassName();
        } else {
            $inheritance = ' extends ' . $this->getClassName();
        }
        $definition .= 'class ' . $this->getMockClassName() .  $inheritance . '{';
        $definition .= Mockery_Mockery::applyTo($reflectedClass);
        $definition .= '}';
        return $definition;
    }

    protected function _createClassDefinition($class)
    {
        $definition = '';
        $definition .= 'class ' . $class .  '{';
        $definition .= Mockery_Mockery::applyTo($class);
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
	
    protected static function _orderArguments(array $ctorArguments, ReflectionClass $refMockObject)
    {
    	$refArguments = $refMockObject->getConstructor()->getParameters();
    	$orderedArgumentList = array();
    	foreach($refArguments as $argument)
    	{
    		if (isset($ctorArguments[$argument->getName()]))
    		{
    			$orderedArgumentList[] = $ctorArguments[$argument->getName()];
    		}
    		else if ($argument->isOptional())
    		{
    			$orderedArgumentList[] = $argument->getDefaultValue();
    		}
    		else
    		{
    			throw new Mockery_Exception('Mandatory  argumenet ' . $argument->getName() . ' not specified');
    		}	
    	}
    	return $orderedArgumentList;
    }
}
