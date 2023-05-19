<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class MustBe extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        if (!is_object($actual)) {
            return $this->expected === $actual;
        }

        return $this->expected == $actual;
        // return spl_object_hash($this->expected) === spl_object_hash($actual);
    }

    public function __toString(): string
    {
        return '<MustBe>';
    }
}
