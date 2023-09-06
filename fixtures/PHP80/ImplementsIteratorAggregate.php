<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP80;

use ArrayIterator;
use IteratorAggregate;

class ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator([]);
    }
}
