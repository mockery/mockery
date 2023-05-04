<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class NotAnyOf extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        foreach ($this->expected as $exp) {
            if ($actual === $exp || $actual == $exp) {
                return false;
            }
        }
        return true;
    }

    public function __toString(): string
    {
        return '<AnyOf>';
    }
}
