<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Not extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return $actual !== $this->expected;
    }

    public function __toString(): string
    {
        return '<Not>';
    }
}
