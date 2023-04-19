<?php

namespace MockeryTest\Fixture;

class MockeryTest_ClassThatImplementsSerializable implements \Serializable
{
    public function serialize()
    {
    }
    public function unserialize($serialized)
    {
    }
}
