<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class AnyArgs extends AbstractMatcher implements ArgumentListMatcher
{
    public function match(mixed &$actual): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return '<Any Arguments>';
    }
}
