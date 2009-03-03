<?php

class MockMeTest_EmptyClass {}
class MockMeTest_SimpleClass {
    public function get(){}
}
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

class MockMeTest_Album
{
    public $name = 'untitled';
    protected static $genre = '';
    public static function setGenre($genre)
    {
        self::$genre = $genre;
    }
    public static function getGenre()
    {
        return self::$genre;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function hasName()
    {
        return !empty($this->name);
    }
    public function setTerms($term1, $term2)
    {
    }
    public function setArtist(MockMeTest_Artist $artist = null)
    {
    }
}
class MockMeTest_Artist {}

class MockMeTest_Album_Exception extends Exception {}
