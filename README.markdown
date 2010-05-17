Mockery
========

Mockery is a simple but flexible mock object framework for use in unit testing.
It is inspired most obviously by Ruby's flexmock library whose API has been
borrowed from as much as possible.

Mockery is released under a New BSD License.

Mock Objects
------------

In unit tests, mock objects simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do not
yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation.

The benefits of a mock object framework are to allow for the flexible generation
of such mock objects (and stubs). They allow the setting of expected method calls
and return values using a flexible scheme which is capable of capturing every
possible real object behaviour.

Prerequisites
-------------

Mockery requires PHP 5.3 which is its sole prerequisite.

Installation
------------

The preferred installation method is via PEAR. At present no PEAR channel
has been provided but this does not prevent a simple install! The simplest
method of installation is:

    git clone git://github.com/padraic/mockery.git mutateme
    cd mockery
    sudo pear install pear.xml

The above process will install Mockery as a PEAR library.

Simple Example
--------------

Note: Example omits PHPUnit integration since it's still in the works. Instead
we use the setup/teardown test methods to explicity manage mocking.

Imagine we have a Temperature class which samples the temperature of a locale
before reporting an average temperature. The data could come from a web service
or any other data source, but we do not have such a class at present. We can,
however, assume some basic interactions with such a class based on its interaction
with the Temperature class.

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
    
Even without an actual service class, we can see how we expect it to operate.
When writing a test for the Temperature class, we can now substitute a mock
object for the real service which allows us to test the behaviour of the
Temperature class without actually needing a concrete service instance.

    class TemperatureTest extends extends PHPUnit_Framework_TestCase
    {

        public function setup()
        {
            $this->container = new \Mockery\Container;
        }
        
        public function teardown()
        {
            $this->container->mockery_close();
        }
        
        public function testGetsAverageTemperatureFromThreeServiceReadings()
        {
            $service = $this->container->mock('service');
            $service->shouldReceive('readTemp')->times(3)->andReturn(10, 12, 14);
            $temperature = new Temperature($service);
            $this->assertEquals(12, $temperature->average());
        }

    }

We'll cover the API in greater detail below.

PHPUnit Integration
-------------------

PHPUnit integration is currently in progress.

Quick Reference
---------------

Mockery implements a shorthand API when creating a mock. Here's a sampling
of the possible startup methods.

    $mock = \Mockery::mock('foo');
    
Creates a mock object named foo. In this case, foo is a name (not necessarily
a class name) used as a simple identifier when raising exceptions. This creates
a mock object of type \Mockery\Mock and is the loosest form of mock possible.

    $mock = \Mockery::mock(array('foo'=>1,'bar'=>2));
    
Creates an mock object named unknown since we passed no name. However we did
pass an expectation array, a quick method of setting up methods to expect with
their return values.

    $mock = \Mockery::mock('foo', array('foo'=>1,'bar'=>2));
    
Similar to the previous examples, only demonstrating the combination of a name
and expectation array.

    $mock = \Mockery::mock('stdClass');
    
Creates a mock identical to a named mock, except the name is an actual class
name. Creates a simple mock as previous examples show, except the mock
object will inherit the class type, i.e. it will pass type hints or instanceof
evaluations for stdClass. Useful where a mock object must be of a specific
type.

    $mock = \Mockery::mock('FooInterface');
    
You can create mock objects based on any concrete class, abstract class or
even an interface. Again, the primary purpose is to ensure the mock object
inherits a specific type for type hinting.

    $mock = \Mockery::mock('FooInterface', array('foo'=>1,'bar'=>2));
    
Yes, you can use the same quick expectation setup as for named mocks with the
class oriented mock object generation.

    $mock = \Mockery::mock('Foo', array('foo'));
    
Passing a simple array of method names alongside a class/interface name, will
yield a partial mock, where only the methods you wish are actually mocked.

    $mock = \Mockery::mock('Foo', array('foo'), array('foo'=>1));

So long as it's the next array after your partial mock methods, you can also use
the quickie expectation setup for your partial mock.

Expectation Declarations
------------------------

Once you have created a mock object, you'll often want to start defining how
exactly it should behave (and how it should be called). This is where the
Mockery expectation declarations take over.

    shouldReceive(method_name)
    
Declares that the mock expects a call to the given method name. This is the
starting expectation upon which all other expectations and constraints are
appended.

    shouldReceive(method1, method2, ...)
    
Declares a number of expected method calls, all of which will adopt any chained
expectations or constraints.

    shouldReceive(array(method1=>1, method2=>2, ...))
    
Declares a number of expected calls but also their return values. All will
adopt any additional chained expectations or constraints.

    with(arg1, arg2, ...)
    
Adds a constraint that this expectation only applies to method calls which
match the expected argument list. Allows for setting up differing expectations
based on the arguments provided to expected calls.

    withAnyArgs()
    
Declares that this expectation matches a method call regardless of what arguments
are passed. This is set by default.

    withNoArgs()
    
Declares this expectation matches method calls with zero arguments.

    andReturn(value)
    
Sets a value to be returned from the expected method call.

    andReturn(value1, value2, ...)
    
Sets up a sequence of return values. For example, the first call will return
value1 and the second value2. Not that all subsequent calls to a mocked method
will always return the final value (or the only value) given to this declaration.
