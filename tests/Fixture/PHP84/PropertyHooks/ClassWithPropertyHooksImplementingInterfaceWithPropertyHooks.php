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

use function strtoupper;

class ClassWithPropertyHooksImplementingInterfaceWithPropertyHooks implements InterfaceWithPropertyHooks
{
    public string $readable { get => strtoupper($this->writeable); }

    public string $readableAndWriteable {
        get => $this->readable;
        set => $value;
    }

    public string $writeable { set => strtoupper($value); }

    protected string $protectedReadable { get => 'protectedReadable'; }

    protected string $protectedReadableAndWriteable {
        get => 'protectedReadableAndWriteable';
        set { }
    }

    protected string $protectedWriteable { set { } }

    public function getProtectedReadable(): string
    {
        return $this->protectedReadable;
    }

    public function getProtectedReadableAndWriteable(): string
    {
        return $this->protectedReadableAndWriteable;
    }

    public function setProtectedReadableAndWriteable(string $value): void
    {
        $this->protectedReadableAndWriteable = $value;
    }

    public function setProtectedWriteable(string $value): void
    {
        $this->protectedWriteable = $value;
    }
}
