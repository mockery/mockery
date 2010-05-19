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
 
namespace Mockery;

class Generator
{

   /**
    * Generates a Mock Object class with all Mockery methods whose
    * intent is basically to provide the mock object with the same
    * class type hierarchy as a typical instance of the class being
    * mocked.
    *
    *
    */
    public static function createClassMock($className, $mockName = null)
    {
        if (is_null($mockName)) $mockName = uniqid('Mockery_');
        $class = new \ReflectionClass($className);
        $definition = '';
        if ($class->isFinal()) {
            throw new \Mockery\Exception(
                'The class ' . $className . ' is marked final and it is not '
                . 'possible to generate a mock object with its type'
            );
        }
        if ($class->isInterface()) {
            $inheritance = ' implements ' . $className . ', \Mockery\MockInterface';
        } else {
            $inheritance = ' extends ' . $className . ' implements \Mockery\MockInterface';
        }
        $definition .= 'class ' . $mockName . $inheritance . PHP_EOL . '{' . PHP_EOL;
        $definition .= self::applyMockeryTo($class);
        $definition .= PHP_EOL . '}';
        eval($definition);
        $mock = new $mockName();
        return $mock;
    }
    
    /**
     * Add all Mockery methods for mocks to the class being defined
     *
     *
     */
    public static function applyMockeryTo(\ReflectionClass $class)
    {
        $definition = '';
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->isFinal()) {
                throw new \Mockery\Exception(
                    'The method ' . $method->getName()
                    . ' is marked final and it is not possible to generate a '
                    . 'mock object with such a method defined'
                );
            }
        }
        /**
         * TODO: Worry about all these other method types later.
         */
        foreach ($methods as $method) {
            if (!$method->isDestructor() 
            && !$method->isStatic()
            && $method->getName() !== '__clone') {
                $definition .= self::_replaceMethod($method);
            }
        }
        $definition .= self::_getStandardMethods();
        return $definition;
    }
    
    /**
     * Attempts to replace defined public (non-static) methods so they all
     * redirect to the Mock Object's __call() interceptor
     *
     * TODO: Add exclusions for partial mock support
     */
    protected static function _replaceMethod(\ReflectionMethod $method)
    {
        $body = '';
        $name = $method->getName();
        if ($name !== '__construct' && $method->isPublic()) {
            $body = '$args = func_get_args();'
                . 'return $this->__call("' . $name . '", $args);';
        }
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
        if ($method->isPublic()) {
            $access = 'public';
        } elseif($method->isProtected()) {
            $access = 'protected';
        } else {
            $access = 'private';
        }
        if ($method->isStatic()) {
            $access .= ' static';
        }
        return $access . ' function ' . $name . '(' . $paramDef . ')'
                          . '{' . $body . '}';
    }
    
    /**
     * NOTE: The code below is taken from Mockery\Mock and should
     * be an exact copy with only one difference - we define the Mockery\Mock
     * constructor as a public init method (since the original class
     * constructor is often not replaceable, e.g. for interface adherance)
     *
     * Return a string def of the standard Mock Object API needed for all mocks
     *
     */
    public static function _getStandardMethods()
    {
        $std = <<<MOCK
    protected \$_mockery_expectations = array();

    protected \$_mockery_lastExpectation = null;
    
    protected \$_mockery_ignoreMissing = false;

    protected \$_mockery_verified = false;

    protected \$_mockery_name = null;

    protected \$_mockery_allocatedOrder = 0;

    protected \$_mockery_currentOrder = 0;

    protected \$_mockery_groups = array();

    protected \$_mockery_container = null;
    
    protected \$_mockery_partial = null;
    
    protected \$_mockery_disableExpectationMatching = false;

    public function mockery_init(\$name, \Mockery\Container \$container = null, \$partialObject = null)
    {
        \$this->_mockery_name = \$name;
        if(is_null(\$container)) {
            \$container = new \Mockery\Container;
        }
        \$this->_mockery_container = \$container;
        if (!is_null(\$partialObject)) {
            \$this->_mockery_partial = \$partialObject;
        }
    }

    public function shouldReceive()
    {
        \$self =& \$this;
        \$lastExpectation = \Mockery::parseShouldReturnArgs(
            \$this, func_get_args(), function(\$method) use (\$self) {
                \$director = \$self->mockery_getExpectationsFor(\$method);
                if (!\$director) {
                    \$director = new \Mockery\ExpectationDirector(\$method, \$self);
                    \$self->mockery_setExpectationsFor(\$method, \$director);
                }
                \$expectation = new \Mockery\Expectation(\$self, \$method);
                \$director->addExpectation(\$expectation);
                return \$expectation;
            }
        );
        return \$lastExpectation;
    }

    public function shouldIgnoreMissing()
    {
        \$this->_mockery_ignoreMissing = true;
    }
    
    public function shouldExpect(Closure \$closure)
    {
        \$recorder = new \Mockery\Recorder(\$this, \$this->_mockery_partial);
        \$this->_mockery_disableExpectationMatching = true;
        \$closure(\$recorder);
        \$this->_mockery_disableExpectationMatching = false;
        return \$this;
    }

    public function byDefault()
    {
        foreach (\$this->_mockery_expectations as \$director) {
            \$exps = \$director->getExpectations();
            foreach (\$exps as \$exp) {
                \$exp->byDefault();
            }
        }
        return \$this;
    }

    public function __call(\$method, array \$args)
    {
        if (isset(\$this->_mockery_expectations[\$method])
        && !\$this->_mockery_disableExpectationMatching) {
            \$handler = \$this->_mockery_expectations[\$method];
            return \$handler->call(\$args);
        } elseif (!is_null(\$this->_mockery_partial) && method_exists(\$this->_mockery_partial, \$method)) {
            return call_user_func_array(array(\$this->_mockery_partial, \$method), \$args);
        } elseif (\$this->_mockery_ignoreMissing) {
            \$return = new \Mockery\Undefined;
            return \$return;
        }
        throw new \BadMethodCallException(
            'Method ' . \$this->_mockery_name . '::' . \$method . ' does not exist on this mock object'
        );
    }

    public function mockery_verify()
    {
        if (\$this->_mockery_verified) return true;
        \$this->_mockery_verified = true;
        foreach(\$this->_mockery_expectations as \$director) {
            \$director->verify();
        }
    }

    public function mockery_teardown()
    {
        
    }

    public function mockery_allocateOrder()
    {
        \$this->_mockery_allocatedOrder += 1;
        return \$this->_mockery_allocatedOrder;
    }

    public function mockery_setGroup(\$group, \$order)
    {
        \$this->_mockery_groups[\$group] = \$order;
    }

    public function mockery_getGroups()
    {
        return \$this->_mockery_groups;
    }

    public function mockery_setCurrentOrder(\$order)
    {
        \$this->_mockery_currentOrder = \$order;
        return \$this->_mockery_currentOrder;
    }

    public function mockery_getCurrentOrder()
    {
        return \$this->_mockery_currentOrder;
    }

    public function mockery_validateOrder(\$method, \$order)
    {
        if (\$order < \$this->_mockery_currentOrder) {
            throw new \Mockery\Exception(
                'Method ' . \$method . ' called out of order: expected order '
                . \$order . ', was ' . \$this->_mockery_currentOrder
            );
        }
        \$this->mockery_setCurrentOrder(\$order);
    }

    public function mockery_setExpectationsFor(\$method, \Mockery\ExpectationDirector \$director)
    {
        \$this->_mockery_expectations[\$method] = \$director;
    }

    public function mockery_getExpectationsFor(\$method)
    {
        if (isset(\$this->_mockery_expectations[\$method])) {
            return \$this->_mockery_expectations[\$method];
        }
    }

    public function mockery_findExpectation(\$method, array \$args)
    {
        if (!isset(\$this->_mockery_expectations[\$method])) {
            return null;
        }
        \$director = \$this->_mockery_expectations[\$method];
        return \$director->findExpectation(\$args);
    }

    public function mockery_getContainer()
    {
        return \$this->_mockery_container;
    }

    public function mockery_getName()
    {
        return \$this->_mockery_name;
    }
MOCK;
        return $std;
    }
        

}
