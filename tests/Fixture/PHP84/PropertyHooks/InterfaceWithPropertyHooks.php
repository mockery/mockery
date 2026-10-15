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

interface InterfaceWithPropertyHooks
{
    public string $readable { get; }

    public string $readableAndWriteable { get; set; }

    public string $writeable { set; }
}
