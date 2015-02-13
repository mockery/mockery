<?php
namespace Mockery\Matcher;

use InvalidArgumentException;

/**
 * This class provides a matcher that matches two strings while ignoring whitespace.
 *
 * @author Sebastian Knott <sebastian.knott@sparhandy.de>
 */
class EqualsStringIgnoringWhitespace extends MatcherAbstract
{
    /**
     * Constructor.
     *
     * @param string $expected
     *
     * @throws InvalidArgumentException
     */
    public function __construct($expected)
    {
        if (!is_string($expected))
        {
            throw new InvalidArgumentException(
                'EqualsStringIgnoringWhitespace works with strings only.',
                1417530930
            );
        }

        $this->_expected = $this->removeUnwantedWhitespace($expected);
    }

    /**
     * Checks if the actual value matches the expected value.
     * $actual is passed by reference to preserve reference trail (where applicable)
     * back to the original method parameter.
     *
     * @param mixed $actual
     *
     * @return bool
     */
    public function match(&$actual)
    {
        if (!is_string($actual))
        {
            return false;
        }

        $actualWithoutUnwantedWhitespace = $this->removeUnwantedWhitespace($actual);

        return $actualWithoutUnwantedWhitespace === $this->_expected;
    }

    /**
     * Returns a string representation of this Matcher.
     *
     * @return string
     */
    public function __toString()
    {
        return '<EqualsStringIgnoringWhitespace>';
    }

    /**
     * This function replaces all \s \n and \t with one single whitespace. It also
     * trims any leading or trailing whitespace from the string.
     *
     * @param string $expected
     *
     * @return string
     */
    private function removeUnwantedWhitespace(&$expected)
    {
        return mb_ereg_replace('[\\s\\n\\t]+', ' ', trim($expected));
    }
}

