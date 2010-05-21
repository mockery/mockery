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

    git clone git://github.com/padraic/mockery.git
    cd mockery
    sudo pear install package.xml

The above process will install Mockery as a PEAR library.

Simple Example
--------------

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

Note: PHPUnit integration (see below) can remove the need for a teardown() method.

    use \Mockery as m;
    
    class TemperatureTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
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

We'll cover the API in greater detail below.

PHPUnit Integration
-------------------

Mockery was designed as a simple to use standalone mock object framework, so
its need for integration with any testing framework is entirely optional.
To integrate Mockery, you just need to define a teardown() method for your
tests containing the following (you may use a shorter \Mockery namespace alias):

    public function teardown() {
        \Mockery::close();
    }
    
This static call cleans up the Mockery container used by the current test, and
run any verification tasks needed for your expectations.

If you prefer to avoid the need for adding a teardown() everywhere, you can
optionally configure PHPUnit to use Mockery's TestListener which does the exact
same thing as above, only without the extra typing. Here's an example of
the configuration using PHPUnit's XML format for configuration.

    <phpunit bootstrap="./Bootstrap.php">
      <testsuite name="My Test Suite">
        <directory>./</directory>
      </testsuite>
      <listeners>
        <listener class="\Mockery\Adapter\Phpunit\TestListener"
            file="Mockery/Adapter/Phpunit/TestListener.php">
        </listener>
      </listeners>
    </phpunit>
    
For some added brevity when it comes to using Mockery, you can also explicitly
use the Mockery namespace with a shorter alias. For example:

    use \Mockery as m;
    
    class SimpleTest extends extends PHPUnit_Framework_TestCase
    {
        public function testSimpleMock() {
            $mock = m::mock('simple mock');
            $mock->shouldReceive('foo')->with(5, m::any())->once()->andReturn(10);
            $this->assertEquals(10, $mock->foo(5));
        }
    }
    
Mockery ships with an autoloader so you don't need to litter your tests with
require_once() calls. To use it, ensure Mockery is on your include_path and add
the following to your test suite's Bootstrap or TestHelper file:

    require_once 'Mockery/Loader.php';
    $loader = new \Mockery\Loader;
    $loader->register();
    
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

    $mock = \Mockery::mock(new Foo);
    
Passing any real object into Mockery will create a partial mock. Partials assume
you can already create a concrete object, so all we need to do is selectively
override a subset of existing methods (or add non-existing methods!) for
our expectations.

    $mock = \Mockery::mock(new Foo, array('foo'=>1));

You can also use the quickie expectation setup for your partial mock. See the
section later on Creating Partial Mocks for more information.

    $mock = \Mockery::mock('name', function($mock){
        $mock->shouldReceive(method_name);
    });
    
All of the various setup methods may be passed a closure as the final parameter.
The closure will be passed the mock object when called so that expectations
can be setup. Distinct from the later explained default expectations, this
allows for the reuse of expectation setups by storing them to a closure for
execution. Note that all other parameters including quick expectation arrays set
prior to the closure will be used before the closure is called.

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

    shouldExpect(closure)
    
Creates a mock object (only from a partial mock) which is used to create a mock
object recorder. The recorder is a simple proxy to the original object passed
in for mocking. This is passed to the closure, which may run it through a set of
operations which are recorded as expectations on the partial mock. A simple
use case is automatically recording expectations based on an existing usage
(e.g. during refactoring). See examples in a later section.

    with(arg1, arg2, ...)
    
Adds a constraint that this expectation only applies to method calls which
match the expected argument list. You can add a lot more flexibility to argument
matching using the built in matcher classes (see later). For example,
\Mockery::any() matches any argument passed to that position in the with()
parameter list.

It's important to note that this means all expectations attached only apply
to the given method when it is called with these exact arguments. Allows for
setting up differing expectations based on the arguments provided to expected calls.

    withAnyArgs()
    
Declares that this expectation matches a method call regardless of what arguments
are passed. This is set by default unless otherwise specified.

    withNoArgs()
    
Declares this expectation matches method calls with zero arguments.

    andReturn(value)
    
Sets a value to be returned from the expected method call.

    andReturn(value1, value2, ...)
    
Sets up a sequence of return values or closures. For example, the first call will return
value1 and the second value2. Not that all subsequent calls to a mocked method
will always return the final value (or the only value) given to this declaration.

    andReturnUsing(closure, ...)
    
Sets a closure (anonymous function) to be called with the arguments passed to
the method. The return value from the closure is then returned. Useful for some
dynamic processing of arguments into related concrete results. Closures can
queued by passing them as extra parameters as for andReturn(). Note that you
cannot currently mix andReturnUsing() with andReturn().

    andThrow(Exception)
    
Declares that this method will throw the given Exception object when called.

    andThrow(exception_name, message)
    
Rather than an object, you can pass in the Exception class and message to
use when throwing an Exception from the mocked method.

    zeroOrMoreTimes()
    
Declares that the expected method may be called zero or more times. This is
the default for all methods unless otherwise set.

    once()
    
Declares that the expected method may only be called once. Like all other
call count constraints, it will throw a \Mockery\CountValidator\Exception
if breached and can be modified by the atLeast() and atMost() constraints.

    twice()
    
Declares that the expected method may only be called twice.

    times(n)
    
Declares that the expected method may only be called n times.

    never()
    
Declares that the expected method may never be called. Ever!

    atLeast()
    
Adds a minimum modifier to the next call count expectation. Thus
atLeast()->times(3) means the call must be called at least three times (given
matching method args) but never less than three times.

    atMost()
    
Adds a maximum modifier to the next call count expectation. Thus
atMost()->times(3) means the call must be called no more than three times. This
also means no calls are acceptable.

    between(min, max)
    
Sets an expected range of call counts. This is actually identical to using
atLeast()->times(min)->atMost()->times(max) but it provided as a shorthand.
It may be followed by a times() call with no parameter to preserve the
APIs natural language readability.

    ordered()
    
Declares that this method is expected to be called in a specific order in
relation to similarly marked methods. The order is dictated by the order in
which this modifier is actually used when setting up mocks.

    ordered(group)
    
Declares the method as belonging to an order group (which can be named or
numbered). Methods within a group can be called in any order, but the ordered
calls from outside the group are ordered in relation to the group, i.e. you can
set up so that method1 is called before group1 which is in turn called before
method 2.

    globally()
    
When called prior to ordered() or ordered(group), it declares this ordering to
apply across all mock objects (not just the current mock). This allows for dictating
order expectations across multiple mocks.

    byDefault()
    
Marks an expectation as a default. Default expectations are applied unless
a non-default expectation is created. These later expectations immediately
replace the previously defined default. This is useful so you can setup default
mocks in your unit test setup() and later tweak them in specific tests as
needed.

    mock()
    
Returns the current mock object from an expectation chain. Useful where
you prefer to keep mock setups as a single statement, e.g.
    
    $mock = \Mockery::mock('foo')->shouldReceive('foo')->andReturn(1)->mock();
    
Argument Validation
-------------------

The arguments passed to the with() declaration when setting up an expectation
determine the criteria for matching method calls to expectations. Thus, you
can setup up many expectations for a single method, each differentiated by
the expected arguments. Such argument matching is done on a "best fit" basis.
This ensures explicit matches take precedence over generalised matches.

An explicit match is merely where the expected argument and the actual argument
are easily equated (i.e. using === or ==). More generalised matches are possible
using regular expressions, class hinting and the available generic matchers. The
purpose of generalised matchers is to allow arguments be defined in non-explicit
terms, e.g. Mockery::any() passed to with() will match ANY argument in that
position.

Here's a sample of the possibilities.

    with(1)
    
Matches the integer 1. This passes the === test (identical). It does facilitate
a less strict == check (equals) where the string '1' would also match the
argument.

    with(\Mockery::any())
    
Matches any argument. Basically, anything and everything passed in this argument
slot is passed unconstrained.

    with(\Mockery::type('resource'))

Matches any resource, i.e. returns true from an is_resource() call. The Type
matcher accepts any string which can be attached to "is_" to form a valid
type check. For example, \Mockery::type('float') checks using is_float() and
\Mockery::type('callable') uses is_callable(). The Type matcher also accepts
a class or interface name to be used in an instanceof evaluation of the
actual argument.

You may find a full list of the available type checkers at
http://www.php.net/manual/en/ref.var.php

    with(\Mockery::on(closure))
    
The On matcher accepts a closure (anonymous function) to which the actual argument
will be passed. If the closure evaluates to (i.e. returns) boolean TRUE then
the argument is assumed to have matched the expectation. This is invaluable
where your argument expectation is a bit too complex for or simply not
implemented in the current default matchers.

    with('/^foo/')
    
The argument declarator also assumes any given string may be a regular
expression to be used against actual arguments when matching. The regex option
is only used when a) there is no === or == match and b) when the regex
is verified to be a valid regex (i.e. does not return false from preg_match()).

    with(\Mockery::ducktype('foo', 'bar'))
    
The Ducktype matcher is an alternative to matching by class type. It simply
matches any argument which is an object containing the provided list
of methods to call.

    with(\Mockery::mustBe(2));
    
The MustBe matcher is more strict than the default argument matcher. The default
matcher allows for PHP type casting, but the MustBe matcher also verifies that
the argument must be of the same type as the expected value. Thus by default,
the argument '2' matches the actual argument 2 (integer) but the MustBe matcher
would fail in the same situation since the expected argument was a string and
instead we got an integer.

Note: Objects are not subject to an identical comparison using this matcher
since PHP would fail the comparison if both objects were not the exact same
instance. This is a hindrance when objects are generated prior to being
returned, since an identical match just would never be possible.

    with(\Mockery::not(2))

The Not matcher matches any argument which is not equal or identical to the
matcher's parameter.

    with(\Mockery::anyOf(1, 2))
    
Matches any argument which equals any one of the given parameters.

    with(\Mockery::notAnyof(1, 2))
    
Matches any argument which is not equal or identical to any of the given
parameters.

    with(\Mockery::subset(array(0=>'foo')))
    
Matches any argument which is any array containing the given array subset. This
enforces both key naming and values, i.e. both the key and value of each
actual element is compared.

    with(\Mockery::contains(value1, value2))
    
Matches any argument which is an array containing the listed values. The naming
of keys is ignored.

    with(\Mockery::hasKey(key));
    
Matches any argument which is an array containing the given key name.

    with(\Mockery::hasValue(key));
    
Matches any argument which is an array containing the given value.

Creating Partial Mocks
----------------------

Partial mocks are useful when you only need to mock several methods of an object
leaving the remainder free to respond to calls normally (i.e. as implemented).

Unlike other mock objects, a Mockery partial mock has a real concrete object
at its heart. This approach to partial mocks is intended to bypass a number
of troublesome issues with partials. For example, partials might require
constructor parameters and other setup/injection tasks prior to use. Trying
to perform this automatically via Mockery is not a tenth as intuitive as just
doing it normally - and then passing the object into Mockery.

Partial mocks are therefore constructed as a Proxy with an embedded real object.
The Proxy itself inherits the type of the embedded object (type safety) and
it otherwise behaves like any other Mockery-based mock object, allowing you to
dynamically define expectations. This flexibility means there's little
upfront defining (besides setting up the real object - you can set defaults,
expectations and ordering on the fly.

Default Mock Expectations
-------------------------

Often in unit testing, we end up with sets of tests which use the same object
dependency over and over again. Rather than mocking this class/object within
every single unit test (requiring a mountain of duplicate code), we can instead
define reusable default mocks within the test case's setup() method. This even
works where unit tests use varying expectations on the same or similar mock
object.

How this works, is that you can define mocks with default expectations. Then,
in a later unit test, you can add or fine-tune expectations for that
specific test. Any expectation can be set as a default using the byDefault()
declaration.

Mocking Demeter Chains And Fluent Interfaces
--------------------------------------------

Both of these terms refer to the growing practice of invoking statements
similar to:

    $object->foo()->bar()->zebra()->alpha()->selfDestruct();
    
The long chain of method calls isn't necessarily a bad thing, assuming they
each link back to a local object the calling class knows. Just as a fun example,
Mockery's long chains (after the first shouldReceive() method) all call to the
same instance of \Mockery\Expectation. However, sometimes this is not the case
and the chain is constantly crossing object boundaries.

In either case, mocking such a chain can be a horrible task. To make it easier
Mockery support demeter chain mocking. Essentially, we shortcut through the
chain and return a defined value from the final call. For example, let's
assume selfDestruct() returns the string "Ten!" to $object (an instance of
CaptainsConsole). Here's how we could mock it.

    $mock = \Mockery::mock('CaptainsConsole');
    $mock->shouldReceive('foo->bar->zebra->alpha->selfDestruct')->andReturn('Ten!');
    
The above expectation can follow any previously seen format or expectation, except
that the method name is simply the string of all expected chain calls separated
by "->". Mockery will automatically setup the chain of expected calls with
its final return values, regardless of whatever intermediary object might be
used in the real implementation.

Mock Object Recording
---------------------

In certain cases, you may find that you are testing against an already
established pattern of behaviour, perhaps during refactoring. Rather then hand
crafting mock object expectations for this behaviour, you could instead use
the existing source code to record the interactions a real object undergoes
onto a mock object as expectations - expectations you can then verify against
an alternative or refactored version of the source code.

To record expectations, you need a concrete instance of the class to be mocked.
This can then be used to create a partial mock to which is given the necessary
code to execute the object interactions to be recorded. A simple example is
outline below (we use a closure for passing instructions to the mock).

Here we have a very simple setup, a class (SubjectUser) which uses another class
(Subject) to retrieve some value. We want to record as expectations on our
mock (which will replace Subject later) all the calls and return values of
a Subject instance when interacting with SubjectUser.

    class Subject {

        public function execute() {
            return 'executed!';
        }
    }

    class SubjectUser {

        public function use(Subject $subject) {
            return $subject->execute();
        }
    }

Here's the test case showing the recording:

    class SubjectUserTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
        {
            \Mockery::close();
        }
        
        public function testSomething()
        {
            $mock = \Mockery::mock(new Subject);
            $mock->shouldExpect(function ($subject) {
                $user = new SubjectUser;
                $user->use($subject);
            });
            
            /**
             * Assume we have a replacement SubjectUser called NewSubjectUser.
             * We want to verify it behaves identically to SubjectUser, i.e.
             * it uses Subject in the exact same way
             */
            $newSubject = new NewSubjectUser;
            $newSubject->use($mock);
        }

    }
    
After the \Mockery::close() call in teardown() validates the mock object, we
should have zero exceptions if NewSubjectUser acted on Subject in a similar way
to SubjectUser. By default the order of calls are not enforced, and loose argument
matching is enabled, i.e. arguments may be equal (==) but not necessarily identical
(===).

If you wished to be more strict, for example ensuring the order of calls
and the final call counts were identical, or ensuring arguments are completely
identical, you can invoke the recorder's strict mode from the closure block, e.g.

    $mock->shouldExpect(function ($subject) {
        $subject->shouldBeStrict();
        $user = new SubjectUser;
        $user->use($subject);
    });
    
Dealing with Final Classes/Methods
----------------------------------

One of the primary restrictions of mock objects in PHP, is that mocking classes
or methods marked final is hard. The final keyword prevents methods so marked
from being replaced in subclasses (subclassing is how mock objects can inherit
the type of the class or object being mocked.

The simplest solution is not to mark classes or methods as final!

However, in a compromise between mocking functionality and type safety, Mockery
does allow creating partial mocks from classes marked final, or from classes with
methods marked final. This offers all the usual mock object goodness but the
resulting mock will not inherit the class type of the object being mocked, i.e.
it will not pass any instanceof comparison.

Mockery Global Configuration
----------------------------

To allow for a degree of fine-tuning, Mockery utilises a singleton configuration
object to store a small subset of core behaviours. The two currently present
include:

* Allowing the mocking of methods which do not actually exist
* Allowing the existence of expectations which are never fulfilled (i.e. unused)

By default, these behaviours are enabled. Of course, there are situations where
this can lead to unintended consequences. The mocking of non-existent methods
may allow mocks based on real classes/objects to fall out of sync with the
actual implementations, especially when some degree of integration testing (testing
of object wiring) is not being performed. Allowing unfulfilled expectations means
unnecessary mock expectations go unnoticed, cluttering up test code, and
potentially confusing test readers.

You may allow or disallow these behaviours (whether for whole test suites or just
select tests) by using one or both of the following two calls:

    \Mockery::getConfiguration()->allowMockingNonExistentMethods(bool);
    \Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(bool);
    
Passing a true allows the behaviour, false disallows it. Both take effect
immediately until switched back. In both cases, if either
behaviour is detected when not allowed, it will result in an Exception being
thrown at that point. Note that disallowing these behaviours should be carefully
considered since they necessarily remove at least some of Mockery's flexibility.

Quick Examples
--------------

Create a mock object to return a sequence of values from a set of method calls.

    class SimpleTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
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
    
Create a mock object which returns a self-chaining Undefined object for a method
call.

    use \Mockery as m;
    
    class UndefinedTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
        {
            m::close();
        }
        
        public function testUndefinedValues()
        {
            $mock = m::mock('my mock');
            $mock->shouldReceive('divideBy')->with(0)->andReturnUndefined();
            $this->assertTrue($mock->divideBy(0) instanceof \Mockery\Undefined);
        }

    }
    
Creates a mock object which multiple query calls and a single update call

    use \Mockery as m;
    
    class DbTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
        {
            m::close();
        }
        
        public function testDbAdapter()
        {
            $mock = m::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3);
            $mock->shouldReceive('update')->with(5)->andReturn(NULL)->once();
            
            // test code here using the mock
        }

    }
    
Expect all queries to be executed before any updates.

    use \Mockery as m;
    
    class DbTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
        {
            m::close();
        }
        
        public function testQueryAndUpdateOrder()
        {
            $mock = m::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3)->ordered();
            $mock->shouldReceive('update')->andReturn(NULL)->once()->ordered();
            
            // test code here using the mock
        }

    }
    
Create a mock object where all queries occur after startup, but before finish, and
where queries are expected with several different params.

    use \Mockery as m;
    
    class DbTest extends extends PHPUnit_Framework_TestCase
    {
        
        public function teardown()
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
