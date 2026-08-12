<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\CountValidator;

interface CountValidatorInterface
{
    /**
     * Checks if the validator can accept an additional nth call
     *
     * @param  int  $n
     * @return bool
     */
    public function isEligible($n);

    /**
     * Validate the call count against this validator
     *
     * @param  int  $n
     * @return bool
     */
    public function validate($n);
}
