<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class VerificationDirector
{
    /**
     * @var ReceivedMethodCalls
     */
    private $receivedMethodCalls;

    /**
     * @var VerificationExpectation
     */
    private $verificationExpectation;

    public function __construct(ReceivedMethodCalls $receivedMethodCalls, VerificationExpectation $expectation)
    {
        $this->receivedMethodCalls = $receivedMethodCalls;
        $this->verificationExpectation = $expectation;
    }

    /**
     * @return self
     */
    public function atLeast()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atLeast', []);
    }

    /**
     * @return self
     */
    public function atMost()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atMost', []);
    }

    /**
     * @param  int  $minimum
     * @param  int  $maximum
     * @return self
     */
    public function between($minimum, $maximum)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('between', [$minimum, $maximum]);
    }

    /**
     * @return self
     */
    public function once()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('once', []);
    }

    /**
     * @param  int|null $limit
     * @return self
     */
    public function times($limit = null)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('times', [$limit]);
    }

    /**
     * @return self
     */
    public function twice()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('twice', []);
    }

    /**
     * @return void
     */
    public function verify()
    {
        $this->receivedMethodCalls->verify($this->verificationExpectation);
    }

    /**
     * @param  mixed $args
     * @return self
     */
    public function with(...$args)
    {
        return $this->cloneApplyAndVerify('with', $args);
    }

    /**
     * @return self
     */
    public function withAnyArgs()
    {
        return $this->cloneApplyAndVerify('withAnyArgs', []);
    }

    /**
     * @param  mixed $args
     * @return self
     */
    public function withArgs($args)
    {
        return $this->cloneApplyAndVerify('withArgs', [$args]);
    }

    /**
     * @return self
     */
    public function withNoArgs()
    {
        return $this->cloneApplyAndVerify('withNoArgs', []);
    }

    /**
     * @param  string       $method
     * @param  array<mixed> $args
     * @return self
     */
    protected function cloneApplyAndVerify($method, $args)
    {
        $verificationExpectation = clone $this->verificationExpectation;

        $verificationExpectation->{$method}(...$args);

        $verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

        $verificationDirector->verify();

        return $verificationDirector;
    }

    /**
     * @param  string       $method
     * @param  array<mixed> $args
     * @return self
     */
    protected function cloneWithoutCountValidatorsApplyAndVerify($method, $args)
    {
        $verificationExpectation = clone $this->verificationExpectation;

        $verificationExpectation->clearCountValidators();

        $verificationExpectation->{$method}(...$args);

        $verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

        $verificationDirector->verify();

        return $verificationDirector;
    }
}
