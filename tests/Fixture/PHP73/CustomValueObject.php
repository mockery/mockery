<?php

declare(strict_types=1);

namespace PHP73;

class CustomValueObject implements CustomValueObjectInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}
