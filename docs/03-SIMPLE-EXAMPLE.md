# Simple Example


Imagine we have a `Temperature` class which samples the temperature of a locale
before reporting an average temperature. The data could come from a web service
or any other data source, but we do not have such a class at present. We can,
however, assume some basic interactions with such a class based on its interaction
with the `Temperature` class.

```PHP
class Temperature
{

    public function __construct($service)
    {
        $this->_service = $service;
    }

    public function average()
    {
        $total = 0;
        for ($i=0;$i<3;$i++) {
            $total += $this->_service->readTemp();
        }
        return $total/3;
    }

}
```

Even without an actual service class, we can see how we expect it to operate.
When writing a test for the `Temperature` class, we can now substitute a mock
object for the real service which allows us to test the behaviour of the
`Temperature` class without actually needing a concrete service instance.

Note: PHPUnit integration can remove the need for a `tearDown()` method.

```PHP
use \Mockery as m;

class TemperatureTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testGetsAverageTemperatureFromThreeServiceReadings()
    {
        $service = m::mock('service');
        $service->shouldReceive('readTemp')->times(3)->andReturn(10, 12, 14);

        $temperature = new Temperature($service);

        $this->assertEquals(12, $temperature->average());
    }

}
```



**[&#8592; Previous](02-UPGRADING.md) | [Contents](../README.md#documentation) | [Next &#8594;](04-PHPUNIT-INTEGRATION.md)**
