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
    public static function createReflectedDefinition($className, $mockName)
    {
        $class = new ReflectionClass($class);
        $definition = '';
        if ($class->isFinal()) {
            throw new \Mockery\Exception(
                'The class ' . $className . ' is marked final and it is not '
                'possible to generate a mock object with its type'
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
        return $definition;
    }
    
    /**
     * Add all Mockery methods for mocks to the class being defined
     *
     *
     */
    public static function applyMockeryTo(ReflectionClass $class)
    {
        $definition = '';
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            if ($method->isFinal() && $method->isPublic()) {
                throw new \Mockery\Exception(
                    'The method ' . $method->getName()
                    . ' is marked final and it is not possible to generate a '
                    . 'mock object with such a method defined'
                );
            }
        }
        foreach ($methods as $method) {
            if (!$method->isDestructor() && $method->getName() !== '__clone') { // worry about this later
                $definition .= self::_replaceMethod($method);
            }
        }
        $definition .= self::_getStandardMethods();
        return $definition;
    }
    
    protected static function _replaceMethod(ReflectionMethod $method)
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
        return $access . ' function ' . $mname . '(' . $paramDef . ')'
                          . '{' . $body . '}';
    }
    
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

    public function mockery_init(\$name, \Mockery\Container \$container = null)
    {
        \$this->_mockery_name = \$name;
        if(is_null(\$container)) {
            \$container = new \Mockery\Container;
        }
        \$this->_mockery_container = \$container;
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
        if (isset(\$this->_mockery_expectations[\$method])) {
            \$handler = \$this->_mockery_expectations[\$method];
            return \$handler->call(\$args);
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
