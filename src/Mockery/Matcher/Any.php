<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Any extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return '<Any>';
    }
}
