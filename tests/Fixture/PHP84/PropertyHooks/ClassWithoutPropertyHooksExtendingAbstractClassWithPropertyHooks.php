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

class ClassWithoutPropertyHooksExtendingAbstractClassWithPropertyHooks extends AbstractClassWithPropertyHooks
{
    public string $readable;

    public string $readableAndWriteable;

    public string $writeable;

    protected string $protectedReadable;

    protected string $protectedReadableAndWriteable;

    protected string $protectedWriteable;
}
