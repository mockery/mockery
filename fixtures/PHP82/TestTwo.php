<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP82;

// Acceptable. This accepts objects that implement just
// A, which is a super-set of those that implement A&B.
class TestTwo implements ITest
{
    public function stuff(A|D $arg): void
    {
    }
}
