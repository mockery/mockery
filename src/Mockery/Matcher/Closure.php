<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Closure extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return ($this->expected)($actual) === true;
    }

    public function __toString(): string
    {
        return '<Closure===true>';
    }
}
