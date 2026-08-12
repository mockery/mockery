<?php

declare(strict_types=1);

namespace PHP74;

/** @implements \Iterator<string, bool> */
interface RestrictReturnType extends \Iterator
{
    public function current(): ?bool; // Parent returns "mixed"
    public function key(): ?string;  // Parent returns "mixed"
}
