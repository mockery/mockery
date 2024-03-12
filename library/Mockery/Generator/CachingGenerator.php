<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

class CachingGenerator implements Generator
{
    protected $cache = [];

    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function generate(MockConfiguration $config)
    {
        $hash = $config->getHash();
        if (isset($this->cache[$hash])) {
            return $this->cache[$hash];
        }

        $definition = $this->generator->generate($config);
        $this->cache[$hash] = $definition;

        return $definition;
    }
}
