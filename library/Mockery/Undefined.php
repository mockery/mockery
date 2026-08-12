<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use ReturnTypeWillChange;

use function spl_object_id;

class Undefined
{
    /**
     * Call capturing to merely return this same object.
     *
     * @param  string       $method
     * @param  array<mixed> $args
     * @return static
     */
    public function __call($method, array $args)
    {
        return $this;
    }

    /**
     * Return a string, avoiding E_RECOVERABLE_ERROR.
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return self::class . ':' . spl_object_id($this);
    }
}
