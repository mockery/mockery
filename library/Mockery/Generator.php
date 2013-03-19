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
    protected static $reservedWords = array(
        "__halt_compiler", "abstract", "and", "array", "as",
        "break", "callable", "case", "catch", "class",
        "clone", "const", "continue", "declare", "default",
        "die", "do", "echo", "else", "elseif",
        "empty", "enddeclare", "endfor", "endforeach", "endif",
        "endswitch", "endwhile", "eval", "exit", "extends",
        "final", "for", "foreach", "function", "global",
        "goto", "if", "implements", "include", "include_once",
        "instanceof", "insteadof", "interface", "isset", "list",
        "namespace", "new", "or", "print", "private",
        "protected", "public", "require", "require_once", "return",
        "static", "switch", "throw", "trait", "try",
        "unset", "use", "var", "while", "xor"
    );

   /**
    * Generates a Mock Object class with all Mockery methods whose
    * intent is basically to provide the mock object with the same
    * class type hierarchy as a typical instance of the class being
    * mocked.
    *
    * @param string $className
    * @param string $mockName
    * @param string $allowFinal
    * @return string Classname of the mock class created
    */
    public static function createClassMock($className, $mockName = null,
        $allowFinal = false, $block = array(), $makeInstanceMock = false,
        $partialMethods = array())
    {
        if (is_null($mockName)) $mockName = uniqid('Mockery_');
        $definition = '';
        $inheritance = '';
        $interfaceInheritance = array();
        $classData = array();
        $classNameInherited = '';
        $classIsFinal = false;
        $callTypehinting = false;
        $useStandardMethods = true;
        if (is_array($className)) {
            foreach ($className as $interface) {
                $class = new \ReflectionClass($interface);
                $classData[] = self::_analyseClass($class, $interface, $allowFinal);
            }
        } else {
            $class = new \ReflectionClass($className);
            $classData[] = self::_analyseClass($class, $className, $allowFinal);
        }
        foreach ($classData as $i=>$data) {
            if ($data['class']->isInterface() && preg_match("/^Traversable$/i", $data['class']->getName())) {
                $classData[] = $iterator = self::_analyseClass(
                    new \ReflectionClass('Iterator'), // can't use Traversable directly so substitute
                    '\Iterator',
                    $allowFinal
                );
                array_unshift($interfaceInheritance, $iterator['className']);
                unset($classData[$i]); // throw away Traversable or we'll get fatal error
            } elseif ($data['class']->isInterface()) {
                $interfaceInheritance[] = $data['className'];
            } elseif ($data['class']->isFinal()) {
                $inheritance = ' extends ' . $data['className'];
                $classNameInherited = $data['className'];
                $classIsFinal = true;
            } else {
                $inheritance = ' extends ' . $data['className'] . ' implements \Mockery\MockInterface';
                $classNameInherited = $data['className'];
            }
        }
        if (count($interfaceInheritance) > 0) {
            foreach ($classData as $i => $data) {
                if ($data['class']->isInterface()) {
                    $extendedInterfaces = $data['class']->getInterfaces();
                    $traversables = preg_grep("/^Traversable$/i", array_keys($extendedInterfaces));
                    if (!empty($traversables) && !in_array('\Iterator', $interfaceInheritance)
                    && !array_key_exists('IteratorAggregate', $extendedInterfaces)
                    && !preg_match("/^Iterator|IteratorAggregate$/i", $data['class']->getName())) {
                        array_unshift($interfaceInheritance, '\Iterator'); // must declare prior to Traversable
                        $classData[] = $iterator = self::_analyseClass(
                            new \ReflectionClass('Iterator'),
                            '\Iterator',
                            $allowFinal
                        );
                    }
                }
            }
            if (!$classIsFinal) $interfaceInheritance[] = '\Mockery\MockInterface';
            if (strlen($classNameInherited) > 0) $inheritance = ' extends ' . $classNameInherited;
            $inheritance .= ' implements ' . implode(', ', $interfaceInheritance);
        }

        $definition .= 'class ' . $mockName . $inheritance . PHP_EOL . '{' . PHP_EOL;
        foreach ($classData as $data) {
            if (!$data['class']->isFinal()) {
                $result = self::applyMockeryTo($data['class'], $data['publicMethods'], $block, $partialMethods);
                if ($result['callTypehinting']) $callTypehinting = true;
                $definition .= $result['definition'];
                $definition .= self::stubAbstractProtected($data['protectedMethods']);
            }  else {
                $useStandardMethods = false;
            }
        }
        if ($useStandardMethods) $definition .= self::_getStandardMethods($callTypehinting, $makeInstanceMock);
        $definition .= PHP_EOL . '}';
        eval($definition);
        return $mockName;
    }

    protected static function _analyseClass($class, $className, $allowFinal = false)
    {
        if ($class->isFinal() && !$allowFinal) {
            throw new \Mockery\Exception(
                'The class ' . $className . ' is marked final and its methods'
                . ' cannot be replaced. Classes marked final can be passed in'
                . ' to \Mockery::mock() as instantiated objects to create a'
                . ' partial mock, but only if the mock is not subject to type'
                . ' hinting checks.'
            );
        } elseif ($class->isFinal()) {
            $className = '\\Mockery\\Mock';
        }
        $hasFinalMethods = false;
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $protected = $class->getMethods(\ReflectionMethod::IS_PROTECTED);
        foreach ($methods as $method) {
            if ($method->isFinal()) {
                $hasFinalMethods = true;
            }
        }
        return array(
            'class' => $class,
            'className' => $className,
            'hasFinalMethods' => $hasFinalMethods,
            'publicMethods' => $methods,
            'protectedMethods' => $protected
        );
    }

    /**
     * Add all Mockery methods for mocks to the class being defined
     *
     *
     */
    public static function applyMockeryTo(\ReflectionClass $class,
        array $methods, array $block, $partialMethods = array())
    {
        $definition = '';
        $callTypehinting = false;
        /**
         * TODO: Worry about all these other method types later.
         */
        foreach ($methods as $method) {
            if(in_array($method->getName(), $block)) continue;
            if (count($partialMethods) > 0 && !in_array(strtolower($method->getName()), $partialMethods)) {
                continue;
            }
            // Skip final methods, i.e. we end up with a partial with final methods untouched
            if ($method->isFinal()) {
                continue;
            }
            if (!$method->isDestructor()
            //&& !$method->isStatic()
            && $method->getName() !== '__call'
            && $method->getName() !== '__clone'
            && $method->getName() !== '__wakeup'
            && $method->getName() !== '__set'
            && $method->getName() !== '__get'
            && $method->getName() !== '__toString'
            && $method->getName() !== '__isset'
            && $method->getName() !== '__callStatic') {
                $definition .= self::_replacePublicMethod($method);
            }
            if ($method->getName() == '__call') {
                $params = $method->getParameters();
                if ($params[1]->isArray()) {
                    $callTypehinting = true;
                }
            }
        }
        return array('definition'=>$definition, 'callTypehinting'=>$callTypehinting);
    }

    public static function stubAbstractProtected(array $methods)
    {
        $definition = '';
        foreach ($methods as $method) {
            if ($method->isAbstract()) {
                $definition .= self::_replaceProtectedAbstractMethod($method);
            }
        }
        return $definition;
    }

    /**
     * Attempts to replace defined public (non-static) methods so they all
     * redirect to the Mock Object's __call() interceptor
     *
     * TODO: Add exclusions for partial mock support
     */
    protected static function _replacePublicMethod(\ReflectionMethod $method)
    {
        $name = $method->getName();

        if (static::_isReservedWord($name)) {
            return " /* Could not replace $name() as it is a reserved word */ ";
        }

        $body = '';
        if ($name !== '__construct' && $method->isPublic()) {
            /**
             * Purpose of this block is to create an argument array where
             * references are preserved (func_get_args() does not preserve
             * references)
             */
            $body = <<<BODY
\$stack = debug_backtrace();
\$args = array();
if (isset(\$stack[0]['args'])) {
    for(\$i=0; \$i<count(\$stack[0]['args']); \$i++) {
        \$args[\$i] =& \$stack[0]['args'][\$i];
    }
}
\$ret = \$this->__call('$name', \$args);
return \$ret;
BODY;
        }
        $methodParams = self::_renderPublicMethodParameters($method);
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
        $returnByRef = $method->returnsReference() ? ' & ' : '';
        return $access . ' function ' . $returnByRef . $name . '(' . $paramDef . ')'
                      . '{' . $body . '}';
    }

    protected static function _renderPublicMethodParameters(\ReflectionMethod $method)
    {
        $class = $method->getDeclaringClass();
        if ($class->isInternal()) { // check for parameter overrides for internal PHP classes
            $paramMap = \Mockery::getConfiguration()
                ->getInternalClassMethodParamMap($class->getName(), $method->getName());
            if (!is_null($paramMap)) return $paramMap;
        }
        $methodParams = array();
        $params = $method->getParameters();
		$typehintMatch = array();
        foreach ($params as $i => $param) {
            $paramDef = '';
            if ($param->isArray()) {
                $paramDef .= 'array ';
            } elseif ($param->getClass()) {
                $paramDef .= $param->getClass()->getName() . ' ';
            }  elseif (preg_match('/^Parameter #[0-9]+ \[ \<(required|optional)\> (?<typehint>\S+ )?.*\$' . $param->getName() . ' .*\]$/', $param->__toString(), $typehintMatch)) {
                if (!empty($typehintMatch['typehint'])) {
                    $paramDef .= $typehintMatch['typehint'] . ' ';
                }
            }
            $paramName = $param->getName();
            if (empty($paramName) || $paramName === '...') {
                $paramName = 'arg' . $i;
            }
            $paramDef .= ($param->isPassedByReference() ? '&' : '') . '$' . $paramName;
            if ($param->isOptional()) {
                if ($param->isDefaultValueAvailable()) {
                    $default = var_export($param->getDefaultValue(), true);
                } else {
                    $default = 'null';
                }
                $paramDef .= ' = ' . $default;
            }

            $methodParams[] = $paramDef;
        }
        return $methodParams;
    }

    /**
     * Replace abstract protected methods (the only enforceable type outside
     * of public methods). The replacement is just a stub that does nothing.
     */
    protected static function _replaceProtectedAbstractMethod(\ReflectionMethod $method)
    {
        $name = $method->getName();

        if (static::_isReservedWord($name)) {
            return " /* Could not replace $name() as it is a reserved word */ ";
        }

        $body = '';
        $methodParams = array();
        $params = $method->getParameters();
        foreach ($params as $param) {
            $paramDef = '';
            if ($param->isArray()) {
                $paramDef .= 'array ';
            } elseif ($param->getClass()) {
                $paramDef .= $param->getClass()->getName() . ' ';
            }
            $paramDef .= ($param->isPassedByReference() ? '&' : '') . '$' . $param->getName();
            if ($param->isDefaultValueAvailable()) {
                $default = var_export($param->getDefaultValue(), true);
                if ($default == '') {
                  $default = 'null';
                }
                $paramDef .= ' = ' . $default;
            } else if ($param->isOptional()) {
                $paramDef .= ' = null';
            }
            $methodParams[] = $paramDef;
        }
        $paramDef = implode(',', $methodParams);
        $access = 'protected';
        $returnByRef = $method->returnsReference() ? ' & ' : '';
        return $access . ' function ' . $returnByRef . $name . '(' . $paramDef . ')'
                      . '{' . $body . '}';
    }

    public static function _isReservedWord($word)
    {
        static $flippedReservedWords;

        if (null === $flippedReservedWords) {
            $flippedReservedWords = array_fill_keys(static::$reservedWords, true);
        }

        return isset($flippedReservedWords[$word]);
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
    public static function _getStandardMethods($callTypehint = true, $makeInstanceMock = false)
    {
        $typehint = $callTypehint ? 'array' : '';
        $std = <<<MOCK
    protected static \$_mockery_staticClassName = '';

    protected \$_mockery_expectations = array();

    protected \$_mockery_lastExpectation = null;

    protected \$_mockery_ignoreMissing = false;

    protected \$_mockery_ignoreMissingAsUndefined = false;

    protected \$_mockery_deferMissing = false;

    protected \$_mockery_verified = false;

    protected \$_mockery_name = null;

    protected \$_mockery_allocatedOrder = 0;

    protected \$_mockery_currentOrder = 0;

    protected \$_mockery_groups = array();

    protected \$_mockery_container = null;

    protected \$_mockery_partial = null;

    protected \$_mockery_disableExpectationMatching = false;

    protected \$_mockery_mockableMethods = array();

    protected \$_mockery_mockableProperties = array();

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
        if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
            if (isset(\$this->_mockery_partial)) {
                \$reflected = new \ReflectionObject(\$this->_mockery_partial);
            } else {
                \$reflected = new \ReflectionClass(\$this->_mockery_name);
            }
            \$methods = \$reflected->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach (\$methods as \$method) {
                if (!\$method->isStatic()) \$this->_mockery_mockableMethods[] = \$method->getName();
            }
        }
    }

    public function shouldReceive()
    {
        \$self = \$this;
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

    public function shouldDeferMissing()
    {
        \$this->_mockery_deferMissing = true;
        return \$this;
    }

    public function makePartial()
    {
        return \$this->shouldDeferMissing();
    }

    public function shouldIgnoreMissing()
    {
        \$this->_mockery_ignoreMissing = true;
        return \$this;
    }

    public function asUndefined()
    {
        \$this->_mockery_ignoreMissingAsUndefined = true;
        return \$this;
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

    public function __call(\$method, $typehint \$args)
    {
        if (isset(\$this->_mockery_expectations[\$method])
        && !\$this->_mockery_disableExpectationMatching) {
            \$handler = \$this->_mockery_expectations[\$method];
            return \$handler->call(\$args);
        } elseif (!is_null(\$this->_mockery_partial) && method_exists(\$this->_mockery_partial, \$method)) {
            return call_user_func_array(array(\$this->_mockery_partial, \$method), \$args);
        } elseif (\$this->_mockery_deferMissing && is_callable("parent::\$method")) {
            return call_user_func_array("parent::\$method", \$args);
        } elseif (\$this->_mockery_ignoreMissing) {
            if (\$this->_mockery_ignoreMissingAsUndefined === true) {
                \$undef = new \Mockery\Undefined;
                return call_user_func_array(array(\$undef, \$method), \$args);
            } else {
                return null;
            }
        }
        throw new \BadMethodCallException(
            'Method ' . \$this->_mockery_name . '::' . \$method . '() does not exist on this mock object'
        );
    }

    /**
     * Forward calls to this magic method to the __call method
     */
    public function __toString()
    {
        return \$this->__call('__toString', array());
    }

    public function mockery_verify()
    {
        if (\$this->_mockery_verified) return true;
        if (isset(\$this->_mockery_ignoreVerification)
        && \$this->_mockery_ignoreVerification == true) {
            return true;
        }
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
        if (isset(\$this->_mockery_ignoreVerification)
        && \$this->_mockery_ignoreVerification === false) {
            return;
        }
        if (\$order < \$this->_mockery_currentOrder) {
            \$exception = new \Mockery\Exception\InvalidOrderException(
                'Method ' . \$this->_mockery_name . '::' . \$method . '()'
                . ' called out of order: expected order '
                . \$order . ', was ' . \$this->_mockery_currentOrder
            );
            \$exception->setMock(\$this)
                ->setMethodName(\$method)
                ->setExpectedOrder(\$order)
                ->setActualOrder(\$this->_mockery_currentOrder);
            throw \$exception;
        }
        \$this->mockery_setCurrentOrder(\$order);
    }

    public function mockery_getExpectationCount()
    {
        \$count = 0;
        foreach(\$this->_mockery_expectations as \$director) {
            \$count += \$director->getExpectationCount();
        }
        return \$count;
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

    public function mockery_getMockableMethods()
    {
        return \$this->_mockery_mockableMethods;
    }

    public function mockery_getMockableProperties()
    {
        return \$this->_mockery_mockableProperties;
    }

    //** Everything below this line is not copied from/needed for Mockery/Mock **//

    public function __wakeup()
    {
        /**
         * This does not add __wakeup method support. It's a blind method and any
         * expected __wakeup work will NOT be performed. It merely cuts off
         * annoying errors where a __wakeup exists but is not essential when
         * mocking
         */
    }

    public static function __callStatic(\$method, $typehint \$args)
    {
        try {
            \$associatedRealObject = \Mockery::fetchMock(__CLASS__);
            return \$associatedRealObject->__call(\$method, \$args);
        } catch (\BadMethodCallException \$e) {
            throw new \BadMethodCallException(
                'Static method ' . \$associatedRealObject->mockery_getName() . '::' . \$method
                . '() does not exist on this mock object'
            );
        }
    }

    public function mockery_getExpectations()
    {
        return \$this->_mockery_expectations;
    }

    public function __isset(\$name)
    {
        if (false === stripos(\$name, '_mockery_') && method_exists(get_parent_class(\$this), '__isset')) {
            return parent::__isset(\$name);
        }
    }

MOCK;
        /**
         * Note: An instance mock allows the declaration of an instantiable class
         * which imports cloned expectations from an existing mock object. In effect
         * it enables pseudo-overloading of the "new" operator.
         */
        if ($makeInstanceMock) {
            $mim = <<<MOCK

    protected \$_mockery_ignoreVerification = true;

    public function __construct()
    {
        \$this->_mockery_ignoreVerification = false;
        \$associatedRealObject = \Mockery::fetchMock(__CLASS__);
        \$directors = \$associatedRealObject->mockery_getExpectations();
        foreach (\$directors as \$method=>\$director) {
            \$expectations = \$director->getExpectations();
            // get the director method needed
            \$existingDirector = \$this->mockery_getExpectationsFor(\$method);
            if (!\$existingDirector) {
                \$existingDirector = new \Mockery\ExpectationDirector(\$method, \$this);
                \$this->mockery_setExpectationsFor(\$method, \$existingDirector);
            }
            foreach (\$expectations as \$expectation) {
                \$clonedExpectation = clone \$expectation;
                \$existingDirector->addExpectation(\$clonedExpectation);
            }
        }
        \Mockery::getContainer()->rememberMock(\$this);
    }
MOCK;
            $std .= $mim;
        }
        return $std;
    }


}
