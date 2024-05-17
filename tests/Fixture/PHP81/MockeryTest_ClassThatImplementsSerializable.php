<?php

declare(strict_types=1);

namespace PHP81;

use Serializable;

class MockeryTest_ClassThatImplementsSerializable implements Serializable
{
    public function serialize()
    {
    }

    public function unserialize($serialized)
    {
    }
}
