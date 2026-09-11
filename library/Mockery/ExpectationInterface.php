<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

interface ExpectationInterface
{
    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function andReturn(...$args);

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public function andReturns(...$args);

    /**
     * @return MockInterface
     */
    public function getMock();

    /**
     * @return int
     */
    public function getOrderNumber();
}
