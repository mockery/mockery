<?php

namespace MockeryTest\Fixture\PHP80100;

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
