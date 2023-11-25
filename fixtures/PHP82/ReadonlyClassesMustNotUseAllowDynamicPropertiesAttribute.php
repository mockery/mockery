<?php

namespace Mockery\Tests\Fixtures\PHP82;

/**
 * @see https://php.watch/versions/8.2/readonly-classes#AllowDynamicProperties
 */
#[AllowDynamicProperties]
readonly class ReadonlyClassesMustNotUseAllowDynamicPropertiesAttribute
{
    public string $test;
}
