<?php

class Mockery_Mockery {

    protected static $_added = array(
        'shouldReceive',
        'mockery_verify',
        'mockery_setVerifiedStatus',
        'mockery_getOrderedNumberNext',
        'mockery_call',
        'mockery_getOrderedNumber',
        'mockery_incrementOrderedNumber'
    );

    protected static $_standardMethods = null;

    public static function applyTo(ReflectionClass $reflectedClass)
    {
        $mockeryDefinition = '';
        $methods = $reflectedClass->getMethods();
        foreach ($methods as $method) {
            if (!$method->isFinal() && !$method->isDestructor()
            && $method->getName() !== '__clone') {
                $mockeryDefinition .= self::_replaceMethod($method);
            }
        }
        $mockeryDefinition .= self::_getStandardMethods();
        return $mockeryDefinition;
    }

    protected static function _replaceMethod(ReflectionMethod $method)
    {
        $body = '';
        $mname = $method->getName();
        if ($mname !== '__construct' && $method->isPublic()) {
            $body = '$store = Mockery_Store::getInstance(spl_object_hash($this));'
                . '$directors = $store->directors;'
                . '$args = func_get_args();'
                . 'return $this->mockery_call("' . $mname . '", $args);';
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

    protected static function _getStandardMethods()
    {
        if (self::$_standardMethods === null) {
            self::$_standardMethods = file_get_contents(dirname(__FILE__).'/Templates/Methods');
        }
        return self::$_standardMethods;
    }

}
