<?php

namespace Mockery\Generator;

class MockDefinition 
{
    protected $config;
    protected $className;
    protected $code;

    public function __construct(MockConfiguration $config, $className, $code)
    {
        $this->config = $config;
        $this->className = $className;
        $this->code = $code;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getCode()
    {
        return $this->code;
    }
}
