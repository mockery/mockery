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
                'body' => 'if (!isset($this->_expectations[$methodName])) {'
                   . '$this->_expectations[$methodName] = new MockMe_Expectation_Director($methodName);'
                   . '}'
                   . '$expectation = new MockMe_Expectation($methodName, $this);'
                   . '$this->_expectations[$methodName]->addExpectation($expectation);'
                   . 'return $expectation;'
            )
        );
        return $methods;
    }

}
