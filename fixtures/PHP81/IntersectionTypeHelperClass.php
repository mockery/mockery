<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP81;

class IntersectionTypeHelperClass implements IntersectionTypeHelper1Interface, IntersectionTypeHelper2Interface
{
    public function foo(): int
    {
        return 123;
    }

    public function bar(): int
    {
        return 123;
    }
}
