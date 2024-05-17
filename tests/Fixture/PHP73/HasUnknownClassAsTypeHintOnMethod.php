<?php

namespace PHP73;

class HasUnknownClassAsTypeHintOnMethod
{
    public function foo(\UnknownTestClass\Bar $bar)
    {
    }
}
