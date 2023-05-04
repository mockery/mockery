<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class HasValue extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return in_array($this->expected, $actual);
    }

    public function __toString(): string
    {
        return sprintf('<HasValue[%s]>', $this->expected);
    }
}
