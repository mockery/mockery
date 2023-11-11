<?php

namespace Mockery\Matcher;

interface Matcher
{
    /**
     * Check if the actual value matches the expected.
     * Actual passed by reference to preserve reference trail (where applicable)
     * back to the original method parameter.
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual);

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString();
}
