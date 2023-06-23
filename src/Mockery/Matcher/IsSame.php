<?php

namespace Mockery\Matcher;

class IsSame extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return $this->expected === $actual;
    }

    public function __toString(): string
    {
        return '<IsSame>';
    }
}
