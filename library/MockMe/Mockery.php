<?php

class MockMe_Mockery {

    protected static $_tracker = array();

    protected static $_added = array(
        'shouldReceive',
        'mockme_verify',
        'mockme_setVerifiedStatus',
        'mockme_getOrderedNumberNext',
        'mockme_call',
        'mockme_getOrderedNumber',
        'mockme_incrementOrderedNumber'
    );

    public static function applyTo($className, ReflectionClass $reflectedClass)
    {
        if (in_array($className, self::$_tracker) || in_array($className, self::_getAncestors($className))) {
            return;
        }
        self::$_tracker[] = $className;
        $methods = $reflectedClass->getMethods();
        foreach ($methods as $method) {
            if ($method->isPublic() && !$method->isFinal() && !$method->isDestructor()
            && $method->getName() !== '__clone' && !in_array($method->getName(), array(
            'shouldReceive', 'mockme_verify', 'mockme_setVerifiedStatus', 'mockme_getOrderedNumberNext',
            'mockme_call', 'mockme_getOrderedNumber', 'mockme_incrementOrderedNumber'
            ))) {
                self::_replaceMethod($method, $className);
            }
        }
        $methods = self::_getMethods();
        // looks clunky, but runkit fucks around with ReflectionClass methods
        $hasMethods = array();
        $invisibleMethods = $reflectedClass->getMethods();
        foreach ($invisibleMethods as $invisibleMethod) {
            $hasMethods[] = $invisibleMethod->getName();
        }
        foreach ($methods as $method) {
            if (in_array($method['name'], $hasMethods)) {
                continue;
            }
            runkit_method_add($className, $method['name'], $method['args'], $method['body'], $method['access']);
        }
    }

    public static function reverseOn($className)
    {
        $reflectedClass = new ReflectionClass($className);
        $methods = $reflectedClass->getMethods();
        foreach ($methods as $method) {
            if (in_array($method->getName(), array(
            'shouldReceive', 'mockme_verify', 'mockme_setVerifiedStatus', 'mockme_getOrderedNumberNext',
            'mockme_call', 'mockme_getOrderedNumber', 'mockme_incrementOrderedNumber'
            ))) {
                runkit_method_remove($className, $method->getName());
            }
            $assumedPreservedName = $method->getName().md5($method->getName());
            if (method_exists($className, $assumedPreservedName)) {
                runkit_method_remove($className, $method->getName());
                runkit_method_rename($className, $assumedPreservedName, $method->getName());
            }
        }
    }

    protected static function _replaceMethod(ReflectionMethod $method, $className)
    {
        $body = '';
        if ($method->getName() !== '__construct') {
            $body = '$args = func_get_args();'
                . 'return $this->mockme_call("' . $method->getName() . '", $args);';
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
        runkit_method_rename($className, $method->getName(), $method->getName().md5($method->getName()));
        runkit_method_add($className, $method->getName(), $paramDef, $body, RUNKIT_ACC_PUBLIC);
    }

    protected static function _getMethods()
    {
        $methods = array(
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'shouldReceive',
                'args' => '$methodName',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                    . 'if (!isset($store->expectations[$methodName])) {'
                    . '    $store->expectations[$methodName] = new MockMe_Director($methodName);'
                    . '}'
                    . '$expectation = new MockMe_Expectation($methodName, $this);'
                    . '$store->expectations[$methodName]->addExpectation($expectation);'
                    . 'return $expectation;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_verify',
                'args' => '',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                    . 'if ($store->verified) {'
                    . '    return $store->verified;'
                    . '}'
                    . '$store->verified = true;'
                    . 'foreach ($store->expectations as $methodName => $director) {'
                    . '    $director->verify();'
                    . '}'
                    . 'return $store->verified;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_setVerifiedStatus',
                'args' => '$bool',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                    . '$store->verified = $bool;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_call',
                'args' => '$methodName, array $args',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                    . '$return = null;'
                    . '$return = $store->expectations[$methodName]->call($args, $this);'
                    . 'return $return;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_getOrderedNumberNext',
                'args' => '',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                    . 'if (is_null($store->orderedNumberNext)) {'
                    . '    $store->orderedNumberNext = 1;'
                    . '    return $store->orderedNumberNext;'
                    . '}'
                    . '$store->orderedNumberNext++;'
                    . 'return $store->orderedNumberNext;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_getOrderedNumber',
                'args' => '',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                   . 'return $store->orderedNumber;'
            ),
            array(
                'access' => RUNKIT_ACC_PUBLIC,
                'name' => 'mockme_incrementOrderedNumber',
                'args' => '',
                'body' => '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                   . '$store->orderedNumber++;'
            ),
        );
        return $methods;
    }

    protected static function _getAncestors($class)
    {
        for ($classes = array(); $class = get_parent_class ($class); $classes[] = $class);
        return $classes;
    }

}
