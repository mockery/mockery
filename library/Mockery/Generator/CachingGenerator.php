<?php

namespace Mockery\Generator;

class CachingGenerator implements Generator
{
    protected $generator;
    protected $cache = array();

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
