<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Fixtures;

class MethodWithNullableTypedParameter
{
    public function foo(?string $bar)
    {
    }

    public function bar(string $bar = null)
    {
    }

    public function baz(?string $bar = null)
    {
    }
}
