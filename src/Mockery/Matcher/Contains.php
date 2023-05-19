<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Contains extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        $values = array_values($actual);
        foreach ($this->expected as $expectation) {
            $match = false;
            foreach ($values as $value) {
                if ($expectation === $value || $expectation == $value) {
                    $match = true;
                    break;
                }
            }
            if ($match === false) {
                return false;
            }
        }
        return true;
    }

    public function __toString(): string
    {
        $elements = [];
        foreach ($this->expected as $value) {
            $elements[] = (string) $value;
        }
        return sprintf('<Contains[%s]>', implode(', ', $elements));
    }
}
