<?php

namespace Mockery\Tests\Fixtures\PHP82;

/**
 * @see https://php.watch/versions/8.2/readonly-classes#dynamic-properties
 */
readonly class ReadonlyClassesMustNotUseDynamicProperties
{
    public string $test;
}
