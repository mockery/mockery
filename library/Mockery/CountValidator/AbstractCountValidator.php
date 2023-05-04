<?php

declare(strict_types=1);

namespace Mockery\CountValidator;

use Mockery\Expectation;

abstract class AbstractCountValidator
{
    /**
     * Set Expectation object and upper call limit
     * @param Expectation $expectation Expectation for which this validator is assigned
     * @param int $limit Call count limit
     */
    public function __construct(
        protected readonly Expectation $expectation,
        protected readonly int $limit = 0
    ) {
    }

    /**
     * Checks if the validator can accept an additional nth call
     */
    public function isEligible(int $n): bool
    {
        return ($n < $this->limit);
    }

    /**
     * Validate the call count against this validator
     */
    abstract public function validate(int $n): bool;
}
