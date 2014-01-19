<?php

namespace Mockery\Generator;

interface Generator
{
    /** @returns MockDefinition */
    public function generate(MockConfiguration $config);
}
