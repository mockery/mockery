<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class AnyOf extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return in_array($actual, $this->expected, true);
    }

    public function __toString(): string
    {
        return '<AnyOf>';
    }
}
