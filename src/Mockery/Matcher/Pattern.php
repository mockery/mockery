<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Pattern extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        $result = preg_match($this->expected, (string) $actual);

        if ($result === false) {
            return false;
        }

        return $result >= 1;
    }

    public function __toString(): string
    {
        return '<Pattern>';
    }
}
