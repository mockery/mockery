<?php

class Mockery_Director
{

    protected $_methodName = null;

    protected $_expectations = array();

    public function __construct($methodName)
    {
        $this->_methodName = $methodName;
    }

    public function addExpectation(Mockery_Expectation $expectation)
    {
    	$this->_expectations[] = $expectation;
    }

    public function verify()
    {
        foreach ($this->_expectations as $expectation) {
            $expectation->verify();
        }
    }

    public function call(array $args, $mock)
    {
        $expectation = $this->findExpectation($args);
        if (!empty($expectation)) {
            if ($expectation->isOrdered()) {
                $mock->mockery_incrementOrderedNumber();
            }
        	$return = $expectation->verifyCall($args);
        	return $return;
        } else {
            throw new Mockery_ExpectationException('unable to find a matching expectation for '
                . $this->_methodName . ' indicating this argument list was not expected: '
                . var_export($args, true));
        }
    }

    public function findExpectation(array $args)
    {
        foreach ($this->_expectations as $expectation) {
            if ($expectation->matchArgs($args)) {
            	return $expectation;
            }
        }
    }

}
