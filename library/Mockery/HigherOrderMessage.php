<?php

namespace Mockery;

class HigherOrderMessage 
{
    private $mock;
    private $method;

    public function __construct(MockInterface $mock, $method)
    {
        $this->mock = $mock;
        $this->method = $method;
    }

    /**
     * @return Mockery\Expectation
     */
    public function __call($method, $args)
    {
        $expectation = $this->mock->{$this->method}($method);

        if ($this->method !== "shouldNotHaveReceived") {
            return $expectation->withArgs($args);
        }

        return $expectation;
    }
}
