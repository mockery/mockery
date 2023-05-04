<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class MultiArgumentClosure extends AbstractMatcher implements ArgumentListMatcher
{
    public function match(mixed &$actual): bool
    {
        return ($this->expected)(...$actual) === true;
    }

    public function __toString(): string
    {
        return '<MultiArgumentClosure===true>';
    }
}
