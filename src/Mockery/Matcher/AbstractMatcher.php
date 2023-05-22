<?php

declare(strict_types=1);

namespace Mockery\Matcher;

use Stringable;

abstract class AbstractMatcher implements Stringable
{
    /**
     * Set the expected value
     *
     * @template TExpected
     * @param TExpected $expected The expected value (or part thereof)
     */
    public function __construct(
        protected mixed $expected = null
    ) {
    }

    /**
     * Check if the actual value matches the expected.
     * Actual passed by reference to preserve reference trail (where applicable)
     * back to the original method parameter.
     *
     * @template TActual
     * @param TActual $actual
     */
    abstract public function match(mixed &$actual): bool;

    /**
     * Return a string representation of this Matcher
     */
    abstract public function __toString(): string;
}
