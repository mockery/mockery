<?php

declare(strict_types=1);

namespace PHP81;

class ClassWithNewInInitializer
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
    )
    {
    }
}
