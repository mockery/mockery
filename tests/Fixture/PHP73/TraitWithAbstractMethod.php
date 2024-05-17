<?php

namespace PHP73;

trait TraitWithAbstractMethod
{
    public function baz()
    {
        return $this->doBaz();
    }

    abstract public function doBaz();
}
