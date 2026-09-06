<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

final class PropertyHook
{
    /**
     * @var bool
     */
    private $hasGetHook;

    /**
     * @var bool
     */
    private $hasSetHook;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @param string      $name       The property name (without leading $)
     * @param string      $visibility 'public' or 'protected'
     * @param string|null $type       The full type hint string, e.g. 'string', '?int', or null
     * @param bool        $hasGetHook Whether the property declares a get hook
     * @param bool        $hasSetHook Whether the property declares a set hook
     */
    public function __construct(
        string $name,
        string $visibility,
        ?string $type,
        bool $hasGetHook,
        bool $hasSetHook
    ) {
        $this->name = $name;
        $this->visibility = $visibility;
        $this->type = $type;
        $this->hasGetHook = $hasGetHook;
        $this->hasSetHook = $hasSetHook;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function hasGetHook(): bool
    {
        return $this->hasGetHook;
    }

    public function hasSetHook(): bool
    {
        return $this->hasSetHook;
    }
}
