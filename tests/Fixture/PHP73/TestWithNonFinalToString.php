<?php

namespace PHP73;

class TestWithNonFinalToString
{
    public function __toString()
    {
        return 'bar';
    }
}
