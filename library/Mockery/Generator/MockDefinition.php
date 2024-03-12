<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use InvalidArgumentException;

class MockDefinition
{
    protected $code;

    protected $config;

    public function __construct(MockConfiguration $config, $code)
    {
        if (! $config->getName()) {
            throw new InvalidArgumentException('MockConfiguration must contain a name');
        }

        $this->config = $config;
        $this->code = $code;
    }

    public function getClassName()
    {
        return $this->config->getName();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
