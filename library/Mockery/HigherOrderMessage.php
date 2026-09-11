<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

/**
 * @template TMock of \Mockery\MockInterface
 *
 * @mixin TMock
 */
class HigherOrderMessage
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var MockInterface
     */
    private $mock;

    /**
     * @param TMock  $mock
     * @param string $method
     */
    public function __construct(MockInterface $mock, $method)
    {
        $this->mock = $mock;
        $this->method = $method;
    }

    /**
     * @param string      $method
     * @param list<mixed> $args
     *
     * @return Expectation
     */
    public function __call($method, $args)
    {
        if ('shouldNotHaveReceived' === $this->method) {
            /** @var Expectation */
            return $this->mock->{$this->method}($method, $args);
        }

        /** @var Expectation $expectation */
        $expectation = $this->mock->{$this->method}($method);

        return $expectation->withArgs($args);
    }
}
