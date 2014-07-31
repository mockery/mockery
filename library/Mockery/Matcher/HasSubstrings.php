<?php

namespace Mockery\Matcher;

class HasSubstrings extends MatcherAbstract
{

    /**
     * Check if the actual value matches the expected.
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        foreach ($this->_expected as $needle) {
            if (strpos($actual, $needle) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        $return = '<HasSubstrings[';
        $return .= implode(', ', $this->_expected) . ']>';
        return $return;
    }

}
