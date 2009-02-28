<?php

class MockMe_Mockery {

    public function applyTo($className)
    {
        $methods = $this->_getMethods();
        foreach ($methods as $method) {
            runkit_add_method($className, $method['name'], $method['args'], $method['body'], $method['access']);
        }
    }

    protected function _getMethods()
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
                'name' => '__call',
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

}
