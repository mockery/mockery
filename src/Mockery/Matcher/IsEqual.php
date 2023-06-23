<?php

namespace Mockery\Matcher;

class IsEqual extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return $this->expected == $actual;
    }

    public function __toString(): string
    {
        return '<IsEqual>';
    }
}
