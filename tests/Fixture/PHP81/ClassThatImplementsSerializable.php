<?php

declare(strict_types=1);

namespace PHP81;

class ClassThatImplementsSerializable implements \Serializable
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
