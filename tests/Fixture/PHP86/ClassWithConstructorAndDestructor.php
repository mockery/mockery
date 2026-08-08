<?php

declare(strict_types=1);

namespace PHP86;

class ClassWithConstructorAndDestructor
{
    public function __construct(public int $number) {}

    public function __destruct() {}

    public function number(): int
    {
        return $this->number;
    }

    public function voidMethod(): void
    {
    }

    public function neverMethod(): never
    {
        throw new \RuntimeException('This method never returns.');
    }
}
