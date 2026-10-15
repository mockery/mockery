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

class ClassWithPropertyHooksExtendingAbstractClassWithPropertyHooks extends AbstractClassWithPropertyHooks
{
    public string $readable { get => 'readable'; }

    public string $readableAndWriteable {
        get => 'readableAndWriteable';
        set => $value;
    }

    public string $writeable { set => $value; }

    protected string $protectedReadable { get => 'protectedReadable'; }

    protected string $protectedReadableAndWriteable {
        get => 'protectedReadableAndWriteable';
        set => $value;
    }

    protected string $protectedWriteable { set => $value; }
}
