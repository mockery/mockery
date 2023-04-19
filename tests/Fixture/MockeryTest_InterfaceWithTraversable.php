<?php

namespace MockeryTest\Fixture;

interface MockeryTest_InterfaceWithTraversable extends \ArrayAccess, \Traversable, \Countable
{
    public function self();
}
