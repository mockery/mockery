<?php

class MockMeTest_EmptyClass {}
interface MockMeTest_Interface {}
interface MockMeTest_InterfaceWithAbstractMethod
{
    public function set();
}
abstract class MockMeTest_AbstractWithAbstractMethod
{
    abstract protected function set();
}
interface MockMeTest_InterfaceWithAbstractMethodAndParameters
{
    public function set(array $array, stdClass $container = null);
}
