<?php

namespace PHP74\MagicMethod;

class ClassWithConstructorAndCloneMethod
{
    /**
     * @var string
     */
    public $value = '';

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __clone()
    {
        $this->value .= ' cloned';
    }
}
