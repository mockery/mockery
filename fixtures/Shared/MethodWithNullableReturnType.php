<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Fixtures;

class MethodWithNullableReturnType
{
    public function nonNullablePrimitive(): string
    {
        return 'test';
    }

    public function nullablePrimitive(): ?string
    {
        return null;
    }

    public function nonNullableSelf(): self
    {
        return $this;
    }

    public function nullableSelf(): ?self
    {
        return null;
    }

    public function nonNullableClass(): MethodWithNullableReturnType
    {
        return $this;
    }

    public function nullableClass(): ?MethodWithNullableReturnType
    {
        return null;
    }

    public function nullableInt(): ?int
    {
        return null;
    }

    public function nullableString(): ?string
    {
        return null;
    }
}
