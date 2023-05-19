<?php

declare(strict_types=1);

namespace Mockery\Matcher;

final class Type extends AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        if ($this->expected == 'real') {
            $function = 'is_float';
        } else {
            $function = 'is_' . strtolower((string) $this->expected);
        }

        if (function_exists($function)) {
            return $function($actual);
        }

        if (
            is_string($this->expected) &&
            (class_exists($this->expected) || interface_exists($this->expected))
        ) {
            return $actual instanceof $this->expected;
        }

        return false;
    }

    public function __toString(): string
    {
        return sprintf('<%s>', ucfirst((string) $this->expected));
    }
}
