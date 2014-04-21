# PHPUnit Integration


Mockery was designed as a simple-to-use *standalone* mock object framework, so
its need for integration with any testing framework is entirely optional.
To integrate Mockery, you just need to define a `tearDown()` method for your
tests containing the following (you may use a shorter `\Mockery` namespace alias):

```PHP
protected function tearDown() {
    \Mockery::close();
}
```

This static call cleans up the Mockery container used by the current test, and
run any verification tasks needed for your expectations.

For some added brevity when it comes to using Mockery, you can also explicitly
use the Mockery namespace with a shorter alias. For example:

```PHP
use \Mockery as m;

class SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleMock() {
        $mock = m::mock('simplemock');
        $mock->shouldReceive('foo')->with(5, m::any())->once()->andReturn(10);

        $this->assertEquals(10, $mock->foo(5));
    }

    protected function tearDown() {
        m::close();
    }
}
```

Mockery ships with an autoloader so you don't need to litter your tests with
`require_once()` calls. To use it, ensure Mockery is on your `include_path` and add
the following to your test suite's `Bootstrap.php` or `TestHelper.php` file:

```PHP
require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';

$loader = new \Mockery\Loader;
$loader->register();
```

If you are using Composer, you can simplify this to just including the Composer generated autoloader
file:

```PHP
require __DIR__ . '/../vendor/autoload.php'; // assuming vendor is one directory up
```

To integrate Mockery into PHPUnit and avoid having to call the close method and
have Mockery remove itself from code coverage reports, use this in you suite:

```PHP
// Create Suite
$suite = new PHPUnit_Framework_TestSuite();

// Create a result listener or add it
$result = new PHPUnit_Framework_TestResult();
$result->addListener(new \Mockery\Adapter\Phpunit\TestListener());

// Run the tests.
$suite->run($result);
```

If you are using PHPUnit's XML configuration approach, you can include the following to load the
`TestListener`:

``` XML
<listeners>
    <listener class="\Mockery\Adapter\Phpunit\TestListener"></listener>
</listeners>
```

Make sure Composer's or Mockery's autoloader is present in the bootstrap file or you will need to
also define a "file" attribute pointing to the file of the above `TestListener` class.


## Warning: PHPUnit running tests in separate processes

PHPUnit provides a functionality that allows [tests to run in a separated process]
(http://phpunit.de/manual/4.0/en/appendixes.annotations.html#appendixes.annotations.runTestsInSeparateProcesses),
to ensure better isolation. Mockery verifies the mocks expectations using the
`Mockery::close` method, and provides a PHPUnit listener, that automatically
calls this method for you after every test.

However, this listener is not called in the right process when using PHPUnit's process
isolation, resulting in expectations that might not be respected, but without raising
any `Mockery\Exception`. To avoid this, you cannot rely on the supplied Mockery PHPUnit
`TestListener`, and you need to explicitly calls `Mockery::close`. The easiest solution
to include this call in the `tearDown()` method, as explained previously.



**[&#8592; Previous](03-SIMPLE-EXAMPLE.md) | [Contents](../README.md#documentation) | [Next &#8594;](05-QUICK-REFERENCE.md)**
