<?php

namespace MockeryTest\Fixture\PHP80100;

use MockeryTest\PHP81\Logger;

class ClassWithNewInInitializer
{
    public function __construct(private Logger $logger = new NullLogger())
    {
    }
}
