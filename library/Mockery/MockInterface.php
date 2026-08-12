<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

interface MockInterface extends LegacyMockInterface
{
    /**
     * @param  mixed                                                                                     $something String method name or map of method => return
     * @return ($something is string ? Expectation : ($something is list{} ? HigherOrderMessage : self))
     */
    public function allows($something = []);

    /**
     * @param  mixed                                                            $something String method name (optional)
     * @return ($something is string ? Expectation : ExpectsHigherOrderMessage)
     */
    public function expects($something = null);
}
