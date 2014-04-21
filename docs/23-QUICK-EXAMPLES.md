# Quick Examples


Create a mock object to return a sequence of values from a set of method calls.

```PHP
class SimpleTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testSimpleMock()
    {
        $mock = \Mockery::mock(array('pi' => 3.1416, 'e' => 2.71));
        $this->assertEquals(3.1416, $mock->pi());
        $this->assertEquals(2.71, $mock->e());
    }

}
```

Create a mock object which returns a self-chaining Undefined object for a method
call.

```PHP
use \Mockery as m;

class UndefinedTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testUndefinedValues()
    {
        $mock = m::mock('mymock');
        $mock->shouldReceive('divideBy')->with(0)->andReturnUndefined();
        $this->assertTrue($mock->divideBy(0) instanceof \Mockery\Undefined);
    }

}
```

Creates a mock object which multiple query calls and a single update call

```PHP
use \Mockery as m;

class DbTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testDbAdapter()
    {
        $mock = m::mock('db');
        $mock->shouldReceive('query')->andReturn(1, 2, 3);
        $mock->shouldReceive('update')->with(5)->andReturn(null)->once();

        // test code here using the mock
    }

}
```

Expect all queries to be executed before any updates.

```PHP
use \Mockery as m;

class DbTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testQueryAndUpdateOrder()
    {
        $mock = m::mock('db');
        $mock->shouldReceive('query')->andReturn(1, 2, 3)->ordered();
        $mock->shouldReceive('update')->andReturn(null)->once()->ordered();

        // test code here using the mock
    }

}
```

Create a mock object where all queries occur after startup, but before finish, and
where queries are expected with several different params.

```PHP
use \Mockery as m;

class DbTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testOrderedQueries()
    {
        $db = m::mock('db');
        $db->shouldReceive('startup')->once()->ordered();
        $db->shouldReceive('query')->with('CPWR')->andReturn(12.3)->once()->ordered('queries');
        $db->shouldReceive('query')->with('MSFT')->andReturn(10.0)->once()->ordered('queries');
        $db->shouldReceive('query')->with("/^....$/")->andReturn(3.3)->atLeast()->once()->ordered('queries');
        $db->shouldReceive('finish')->once()->ordered();

        // test code here using the mock
    }

}
```



**[&#8592; Previous](22-GOTCHAS.md) | [Contents](../README.md#documentation)**
