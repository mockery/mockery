<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP81;

use Serializable;

class ClassThatImplementsSerializable implements Serializable
{
    public function serialize(): ?string
    {
    }

    public function __serialize(): array
    {
    }

    public function unserialize(string $data): void
    {
    }

    public function __unserialize(array $data): void
    {
    }
}
