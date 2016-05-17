<?php

namespace Mockery;

class ExpectationBuilder 
{
    private $mock;

    public function __construct(MockInterface $mock)
    {
        $this->mock = $mock;
    }

    /**
     * @return Mockery\Expectation
     */
    public function __call($method, $args)
    {
        return $this->mock->shouldReceive($method)->withArgs($args);
    }
}
