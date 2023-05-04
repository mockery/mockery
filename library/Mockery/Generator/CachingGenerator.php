<?php

declare(strict_types=1);

namespace Mockery\Generator;

final class CachingGenerator implements Generator
{
    /**
     * @var array<string,MockConfiguration>
     */
    private array $cache = [];
    public function __construct(
        private readonly Generator $generator
    ) {
    }

    public function generate(MockConfiguration $config): MockDefinition
    {
        return $this->cache[$config->getHash()] ??= $this->generator->generate($config);
    }
}
