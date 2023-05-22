<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class HasKey extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return array_key_exists($this->expected, $actual);
    }
    public function __toString(): string
    {
        return sprintf('<HasKey[%s]>', $this->expected);
    }
}
