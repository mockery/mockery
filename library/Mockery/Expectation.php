<?php

class Mockery_Expectation
{

    protected $_methodName = null;

    protected $_mockObject = null;

    protected $_expectedCallCount = null;

    protected $_actualCallCount = 0;

    protected $_expectedArgs = array();

    protected $_returnQueue = array();

    protected $_counterClass = 'Mockery_StandardCounter';

    protected $_orderedNumber = null;

    protected $_exceptionToThrow = null;

    protected $_regexArgs = false;

    protected $_counterRange = false;

    public function __construct($methodName, $mockObject)
    {
        $this->_methodName = $methodName;
        $this->_mockObject = $mockObject;
        $this->_expectedCallCount = new $this->_counterClass(1);
    }

    public function verify()
    {
        if (is_array($this->_expectedCallCount)) {
            $descriptions = array();
            foreach ($this->_expectedCallCount as $expectedCallCount) {
                $descriptions[] = $expectedCallCount->getDescription();
            }
            foreach ($this->_expectedCallCount as $expectedCallCount) {
                $this->_verifyCallCount($expectedCallCount, implode(' and ', $descriptions));
            }
        } else {
            $this->_verifyCallCount($this->_expectedCallCount, $this->_expectedCallCount->getDescription());
        }
    }

    protected function _verifyCallCount($expectedCallCount, $description)
    {
        if (!$expectedCallCount->verify($this->_actualCallCount)) {
            $this->_mockObject->mockery_setVerifiedStatus(false);
        	throw new Mockery_ExpectationException(
        	   'method ' . $this->_methodName
        	   .' called incorrect number of times; expected call ' . $description
        	   . ' but received ' . $this->_actualCallCount
        	);
        }
    }

    public function times($times)
    {
        $times = intval($times);
        if ($this->_counterRange === true) {
            if (!is_array($this->_expectedCallCount)) {
                $this->_expectedCallCount = array();
            }
            $this->_expectedCallCount[] = new $this->_counterClass($times);
        } else {
            $this->_expectedCallCount = new $this->_counterClass($times);
        }
        return $this;
    }

    public function once()
    {
        return $this->times(1);
    }

    public function twice()
    {
        return $this->times(2);
    }

    public function never()
    {
        return $this->times(0);
    }

    public function zeroOrMoreTimes()
    {
        $this->_expectedCallCount = new Mockery_ZeroOrMoreCounter();
        return $this;
    }

    public function atLeast()
    {
        $this->_counterRange = true;
        $this->_counterClass = 'Mockery_AtLeastCounter';
        return $this;
    }

    public function atMost()
    {
        $this->_counterRange = true;
        $this->_counterClass = 'Mockery_AtMostCounter';
        return $this;
    }

    public function andReturn()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            $this->_returnQueue[] = $arg;
        }
        return $this;
    }

    public function andThrow($exceptionClass, $message = null)
    {
        if ($exceptionClass !== 'Exception') {
            $reflectedClass = new ReflectionClass($exceptionClass);
            if (!$reflectedClass->isSubclassOf( new ReflectionClass('Exception') )) {
                throw new Mockery_Exception(
                    'andThrow received value "' . $exceptionClass . '" which is not a'
                    . ' class of type "Exception"'
                );
            }
        }
        if (!is_null($message)) {
            $this->_exceptionToThrow = array($exceptionClass, $message);
        } else {
            $this->_exceptionToThrow = $exceptionClass;
        }
        return $this;
    }

    public function with()
    {
        $args = func_get_args();
        $this->_expectedArgs = $args;
        return $this;
    }

    public function withAnyArgs()
    {
        $this->_expectedArgs = true;
        return $this;
    }

    public function withNoArgs()
    {
        $this->_expectedArgs = false;
        return $this;
    }

    public function withArgsMatching()
    {
        $args = func_get_args();
        $this->_expectedArgs = $args;
        $this->_regexArgs = true;
    }

    public function matchArgs(array $args)
    {
        if (empty($args) && empty($this->_expectedArgs) && is_array($this->_expectedArgs)) {
            return true;
        } elseif ($args == $this->_expectedArgs) {
            return true;
        } elseif ($this->_expectedArgs === true) {
            return true;
        } elseif ($this->_expectedArgs === false && empty($args)) {
            return true;
        } elseif ($this->_regexArgs === true && count($args) == count($this->_expectedArgs)) {
            $i = count($args);
            for ($j = 0; $j < $i; $j++) {
                if (is_array($args[$j]) || is_object($args[$j])
                || !preg_match($this->_expectedArgs[$j], $args[$j])) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    public function ordered()
    {
        $this->_orderedNumber = $this->_mockObject->mockery_getOrderedNumberNext();
        return $this;
    }

    public function isOrdered()
    {
        return !is_null($this->_orderedNumber);
    }

    public function verifyCall(array $args)
    {
        $this->_validateOrder();
        $this->_actualCallCount++;
        if (!is_null($this->_exceptionToThrow)) {
            if (is_array($this->_exceptionToThrow)) {
                $class = $this->_exceptionToThrow[0];
                $message = $this->_exceptionToThrow[1];
                throw new $class($message);
            } else {
                throw new $this->_exceptionToThrow;
            }
        }
        return $this->_returnValue();
    }

    protected function _validateOrder()
    {
        if ($this->isOrdered()) {
            $currentOrder = $this->_mockObject->mockery_getOrderedNumber();
            if ($currentOrder !== $this->_orderedNumber) {
                throw new Mockery_ExpectationException(
                    'Method ' . $this->_methodName . ' called out of order; expected at index of '
                    . $this->_orderedNumber . ' but was called at ' . $currentOrder
                );
            }
        }
    }

    protected function _returnValue()
    {
        if (count($this->_returnQueue) == 1) {
            return $this->_returnQueue[0];
        }
        if (count($this->_returnQueue) > 1) {
            return array_shift($this->_returnQueue);
        }
    }

}
