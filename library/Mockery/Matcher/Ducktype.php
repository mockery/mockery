<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Ducktype extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        if (! is_object($actual)) {
            return false;
        }

        foreach ($this->expected as $method) {
            if (!method_exists($actual, $method)) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        return sprintf('<Ducktype[%s]>', implode(', ', $this->expected));
    }
}
