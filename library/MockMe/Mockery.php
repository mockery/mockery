<?php

require_once 'MockMe/Methods.php';

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
            && $method->getName() !== '__clone' && !in_array($method->getName(), self::$_added)) {
                self::_replaceMethod($method, $className);
            }
        }
        foreach (self::$_added as $method) {
            if (method_exists($className, $method)) {
                continue;
            }
            runkit_method_copy($className, $method, 'MockMe_Methods');
        }
    }

    public static function reverseOn($className)
    {
        $reflectedClass = new ReflectionClass($className);
        $methods = $reflectedClass->getMethods();
        foreach ($methods as $method) {
            if (in_array($method->getName(), self::$_added)) {
                runkit_method_remove($className, $method->getName());
            }
            $assumedPreservedName = $method->getName().md5($method->getName());
            if (method_exists($className, $assumedPreservedName)) {
                runkit_method_remove($className, $method->getName());
                runkit_method_rename($className, $assumedPreservedName, $method->getName());
            }
        }
        $key = array_search($className, self::$_tracker);
        if ($key) {
            unset(self::$_tracker[$key]);
        }
    }

    protected static function _replaceMethod(ReflectionMethod $method, $className)
    {
        $body = '';
        $mname = $method->getName();
        if (method_exists($className, $mname.md5($mname))) {
            return;
        }
        if ($mname !== '__construct') {
            $body = '$store = MockMe_Store::getInstance(spl_object_hash($this));'
                . '$directors = $store->directors;'
                . '$args = func_get_args();'
                . 'if(empty($directors)) {'
                . 'return call_user_func_array(array($this, \''. $mname.md5($mname) .'\'), $args);'
                . '}'
                . 'return $this->mockme_call("' . $mname . '", $args);';
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
        runkit_method_copy($className, $mname.md5($mname), $className, $mname);
        runkit_method_redefine($className, $mname, $paramDef, $body);
    }

    protected static function _getAncestors($class)
    {
        for ($classes = array(); $class = get_parent_class ($class); $classes[] = $class);
        return $classes;
    }

}
