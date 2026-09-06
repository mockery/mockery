<?php

namespace PHP74\MagicMethod;

interface InterfaceWithConstructorMethod
{
    public function __construct();
}

// '__construct' => 1,
// '__destruct' => 1,
// '__call' => 1,
// '__callStatic' => 1,
// '__get' => 1,
// '__set' => 1,
// '__isset' => 1,
// '__unset' => 1,
// '__toString' => 1,
// '__invoke' => 1,
class ClassWithConstructorMethod implements InterfaceWithConstructorMethod
{
    public function __construct()
    {
    }
}
// '__destruct' => 1,
interface InterfaceWithDestructMethod
{
    public function __destruct();
}
class ClassWithDestructMethod implements InterfaceWithDestructMethod {
    public function __destruct()
    {
    }
}
// '__call' => 1,
interface InterfaceWithCallMethod
{
    public function __call($name, $arguments);
}
class ClassWithCallMethod implements InterfaceWithCallMethod
{
    public function __call($name, $arguments) {

    }
}
// '__callStatic' => 1,
interface InterfaceWithCallStaticMethod
{
    public static function __callStatic($name, $arguments);
}
class ClassWithCallStaticMethod implements InterfaceWithCallStaticMethod
{
    public static function __callStatic($name, $arguments) {}
}
// '__get' => 1,
interface InterfaceWithGetMethod
{
    public function __get($name);
}
class ClassWithGetMethod implements InterfaceWithGetMethod
{
    public function __get($name) {}
}
// '__set' => 1,
interface InterfaceWithSetMethod
{
    public function __set($name, $value);
}
class ClassWithSetMethod implements InterfaceWithSetMethod
{
    public function __set($name, $value) {}
}
// '__isset' => 1,
interface InterfaceWithIssetMethod
{
    public function __isset($name);
}
class ClassWithIssetMethod implements InterfaceWithIssetMethod
{
    public function __isset($name) {}
}
// '__unset' => 1,
interface InterfaceWithUnsetMethod
{
    public function __unset($name);
}
class ClassWithUnsetMethod implements InterfaceWithUnsetMethod
{
    public function __unset($name) {}
}
// '__toString' => 1,
interface InterfaceWithToStringMethod
{
    public function __toString();
}
class ClassWithToStringMethod implements InterfaceWithToStringMethod
{
    public function __toString() {
        return '';
    }
}
// '__invoke' => 1,
interface InterfaceWithInvokeMethod
{
    public function __invoke();
}
class ClassWithInvokeMethod implements InterfaceWithInvokeMethod
{
    public function __invoke() {}
}
