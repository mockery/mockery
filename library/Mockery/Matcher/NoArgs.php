<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class NoArgs extends AbstractMatcher implements ArgumentListMatcher
{
    public function match(mixed &$actual): bool
    {
        return $actual === [];
    }

    public function __toString(): string
    {
        return '<No Arguments>';
    }
}
