<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP84\PropertyHooks;

abstract class AbstractClassWithPropertyHooks implements InterfaceWithPropertyHooks
{
    abstract public string $readable { get; }

    abstract public string $readableAndWriteable{get; set; }

    abstract public string $writeable{set; }

    abstract protected string $protectedReadable{get; }

    abstract protected string $protectedReadableAndWriteable{get; set; }

    abstract protected string $protectedWriteable{set; }

    public function getProtectedReadable(): string
    {
        return $this->protectedReadable;
    }

    public function getProtectedReadableAndWriteable(): string
    {
        return $this->protectedReadableAndWriteable;
    }

    public function getReadable(): string
    {
        return $this->readable;
    }

    public function getReadableAndWriteable(): string
    {
        return $this->readableAndWriteable;
    }

    public function setProtectedReadable(string $value): void
    {
        $this->protectedReadable = $value;
    }

    public function setProtectedReadableAndWriteable(string $value): void
    {
        $this->protectedReadableAndWriteable = $value;
    }

    public function setProtectedWriteable(string $value): void
    {
        $this->protectedWriteable = $value;
    }

    public function setReadableAndWriteable(string $value): void
    {
        $this->readableAndWriteable = $value;
    }

    public function setWriteable(string $value): void
    {
        $this->writeable = $value;
    }
}
