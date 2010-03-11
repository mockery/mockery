<?php

class MockeryTest_EmptyClass {}
class MockeryTest_SimpleClass {
    public function get(){return 'simple';}
    public function set(){}
}
interface MockeryTest_Interface {}
interface MockeryTest_InterfaceWithAbstractMethod
{
    public function set();
}
abstract class MockeryTest_AbstractWithAbstractMethod
{
    abstract protected function set();
}
interface MockeryTest_InterfaceWithAbstractMethodAndParameters
{
    public function set(array $array, stdClass $container = null);
}

class MockeryTest_Album
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
    public function setArtist(MockeryTest_Artist $artist = null)
    {
    }
}
class MockeryTest_Artist {}

class MockeryTest_Album_Exception extends Exception {}

class MockeryTest_RecordLabel
{
	protected $name;
	protected $founded;
	protected $artists;
	
	public function __construct($name, DateTime $founded, array $artists = array())
	{
		$this->name = $name;
		$this->founded = $founded;
		foreach ($artists as $artist)
		{
			$this->addArtist($artist);
		}
	}
	
	public function addArtist(MockeryTest_Artist $artist)
	{
		if (in_array($artist, $this->artists, true))
		{
			throw new MockeryTest_Album_Exception('Artist ' . $artist->getName() . ' is already with this label.');
		}
	}
	
	public function getName()
	{
		return $this->name;
	}
}
