<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Override;

/**
 * @template TMock of \Mockery\MockInterface
 *
 * @extends HigherOrderMessage<TMock>
 *
 * @mixin TMock
 *
 * @final
 */
class ExpectsHigherOrderMessage extends HigherOrderMessage
{
    /**
     * @param TMock $mock
     */
    public function __construct(MockInterface $mock)
    {
        parent::__construct($mock, 'shouldReceive');
    }

    /**
     * @param string      $method
     * @param list<mixed> $args
     *
     * @return Expectation
     */
    #[Override]
    public function __call($method, $args)
    {
        return parent::__call($method, $args)->once();
    }
}
