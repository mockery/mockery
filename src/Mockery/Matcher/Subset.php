<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Subset extends AbstractMatcher
{
    /**
     * @param array $expected Expected subset of data
     * @param bool $strict Whether to run a strict or loose comparison
     */
    public function __construct(
        array $expected,
        private readonly bool $strict = true
    ) {
        parent::__construct($expected);
    }

    /**
     * @param array $expected Expected subset of data
     */
    public static function strict(array $expected): self
    {
        return new self($expected, true);
    }

    /**
     * @param array $expected Expected subset of data
     */
    public static function loose(array $expected)
    {
        return new self($expected, false);
    }

    public function match(mixed &$actual): bool
    {
        if (!is_array($actual)) {
            return false;
        }

        if ($this->strict) {
            return $actual === array_replace_recursive($actual, $this->expected);
        }

        return $actual == array_replace_recursive($actual, $this->expected);
    }

    public function __toString(): string
    {
        return sprintf('<Subset%s>', $this->formatArray($this->expected));
    }

    /**
     * Recursively format an array into the string representation for this matcher
     *
     * @param array $array
     * @return string
     */
    private function formatArray(array $array): string
    {
        $elements = [];

        foreach ($array as $key => $value) {
            $elements[] = $key . '=' . (is_array($value) ? $this->formatArray($value) : (string) $value);
        }

        return sprintf('[%s]', implode(', ', $elements));
    }
}
