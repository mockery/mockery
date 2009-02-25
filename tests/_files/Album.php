<?php

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
    public function setTerms($term1, $term2)  {}
    public function setArtist(MockMeTest_Artist $artist = null) {}
}

final class MockMeTest_AlbumFinal {}

interface MockMeTest_AlbumInterface {}

interface MockMeTest_AlbumInterfaceWithCtor {
    public function __construct($name);
}

class MockMeTest_Artist {}

class MockMeTest_Album_Exception extends Exception {}
