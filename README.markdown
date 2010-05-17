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
of such mock objects. They allow the setting of expected method calls and
return values using a flexible scheme which is capable of capturing every
possible real object behaviour.

Mock Objects In PHP
-------------------

Mockery has as its goal the intent to supercede all current mock object
implementations in PHP. Mock objects in PHP have always tended to be treated
as second class citizens and the flaws of current implementations have prevented
any noticeable adoption of mock objects beyond their simpler role as Stubs.

This status quo has left PHP in the unique situation where unit testing has
remained tightly tied to real class implementation, despite this being contrary
to modern methodologies such as Test-Driven Design and Behaviour-Driven Testing.
Mockery aims to liberate developers, to offer a mock object framework that rivals
its counterparts in Ruby, Java, (ad infinitum) for ease of use, flexibility,
and completeness in simulating the actions of real objects.

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

    


