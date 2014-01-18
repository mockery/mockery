<?php

namespace Mockery\Generator;

class MockDefinition
{
    protected $config;
    protected $code;

    public function __construct(MockConfiguration $config, $code)
    {
        if (!$config->getName()) {
            throw new \InvalidArgumentException("MockConfiguration must contain a name");
        }
        $this->config = $config;
        $this->code = $code;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getClassName()
    {
        return $this->config->getName();
    }

    public function getCode()
    {
        return $this->code;
    }
}
