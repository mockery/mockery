<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use Override;

use ReturnTypeWillChange;

use function array_values;
use function implode;

class Contains extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        $elements = [];
        foreach ($this->_expected as $expected) {
            $elements[] = (string) $expected;
        }

        return '<Contains[' . implode(', ', $elements) . ']>';
    }

    /**
     * Check if the actual value matches the expected.
     *
     * @param  mixed $actual
     * @return bool
     */
    #[Override]
    public function match(&$actual)
    {
        $values = array_values($actual);

        foreach ($this->_expected as $expected) {
            $match = false;

            foreach ($values as $value) {
                if ($expected === $value || $expected == $value) {
                    $match = true;

                    break;
                }
            }

            if (false === $match) {
                return false;
            }
        }

        return true;
    }
}
