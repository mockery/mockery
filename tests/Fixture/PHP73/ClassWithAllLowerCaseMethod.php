<?php

declare(strict_types=1);

namespace PHP73;

class ClassWithAllLowerCaseMethod
{
    public function userexpectscamelcasemethod()
    {
        return 'real';
    }
}

