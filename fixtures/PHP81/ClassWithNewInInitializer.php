<?php

namespace PHP81;

class ClassWithNewInInitializer
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }
}
