<?php

declare(strict_types=1);

namespace Mockery\Generator;

interface Generator
{
    public function generate(MockConfiguration $config): MockDefinition;
}
