<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class VerificationDirector
{
    private $receivedMethodCalls;
    private $expectation;

    public function __construct(ReceivedMethodCalls $receivedMethodCalls, VerificationExpectation $expectation)
    {
        $this->receivedMethodCalls = $receivedMethodCalls;
        $this->expectation = $expectation;
    }

    public function verify()
    {
        return $this->receivedMethodCalls->verify($this->expectation);
    }

    public function with(...$args)
    {
        return $this->cloneApplyAndVerify("with", $args);
    }

    public function withArgs($args)
    {
        return $this->cloneApplyAndVerify("withArgs", array($args));
    }

    public function withNoArgs()
    {
        return $this->cloneApplyAndVerify("withNoArgs", array());
    }

    public function withAnyArgs()
    {
        return $this->cloneApplyAndVerify("withAnyArgs", array());
    }

    public function times($limit = null)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("times", array($limit));
    }

    public function once()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("once", array());
    }

    public function twice()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("twice", array());
    }

    public function atLeast()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("atLeast", array());
    }

    public function atMost()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("atMost", array());
    }

    public function between($minimum, $maximum)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify("between", array($minimum, $maximum));
    }

    protected function cloneWithoutCountValidatorsApplyAndVerify($method, $args)
    {
        $expectation = clone $this->expectation;
        $expectation->clearCountValidators();
        call_user_func_array(array($expectation, $method), $args);
        $director = new VerificationDirector($this->receivedMethodCalls, $expectation);
        $director->verify();
        return $director;
    }

    protected function cloneApplyAndVerify($method, $args)
    {
        $expectation = clone $this->expectation;
        call_user_func_array(array($expectation, $method), $args);
        $director = new VerificationDirector($this->receivedMethodCalls, $expectation);
        $director->verify();
        return $director;
    }
}
