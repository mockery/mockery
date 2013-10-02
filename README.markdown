Mockery
========
[![Latest Stable Version](https://poser.pugx.org/mockery/mockery/v/stable.png)](https://packagist.org/packages/mockery/mockery) [![Total Downloads](https://poser.pugx.org/mockery/mockery/downloads.png)](https://packagist.org/packages/mockery/mockery)

Mockery is a simple yet flexible PHP mock object framework for use in unit testing
with PHPUnit, PHPSpec or any other testing framework. Its core goal is to offer a
test double framework with a succinct API capable of clearly defining all possible
object operations and interactions using a human readable Domain Specific Language
(DSL). Designed as a drop in alternative to PHPUnit's phpunit-mock-objects library,
Mockery is easy to integrate with PHPUnit and can operate alongside
phpunit-mock-objects without the World ending.

Mockery is released under a New BSD License.

The current released version for PEAR is 0.8.0. Composer users may instead opt to use
the current master branch in lieu of using the more static 0.8.0 git tag.
The build status of the current master branch is tracked by Travis CI:
[![Build Status](https://travis-ci.org/padraic/mockery.png?branch=master)](http://travis-ci.org/padraic/mockery)

Mock Objects
------------

In unit tests, mock objects simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do not
yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation up front.

The benefits of a mock object framework are to allow for the flexible generation
of such mock objects (and stubs). They allow the setting of expected method calls
and return values using a flexible API which is capable of capturing every
possible real object behaviour in way that is stated as close as possible to a
natural language description.

Prerequisites
-------------

Mockery requires PHP 5.3.2 or greater. In addition, it is recommended to install
the Hamcrest library (see below for instructions) which contains additional
matchers used when defining expected method arguments.

Installation
------------

Mockery may be installed using Composer, PEAR or by cloning it from its GitHub repository. These
three options are outlined below.

**Composer**

You can read more about Composer and its main repository at
[http://packagist.org](http://packagist.org "Packagist"). To install
Mockery using Composer, first install Composer for your project using the instructions on the
Packagist home page. You can then define your development dependency on Mockery using the
suggested parameters below. While every effort is made to keep the master branch
stable, you may prefer to use the current stable version tag instead.

    {
        "require-dev": {
            "mockery/mockery": "dev-master@dev"
        }
    }

To install, you then may call:

    composer.phar install --dev

This will install Mockery as a development dependency but will not install it
for regular non-development installs.

**PEAR**

Mockery is hosted on the [survivethedeepend.com](http://pear.survivethedeepend.com) PEAR channel and
can be installed using the following commands:

    sudo pear channel-discover pear.survivethedeepend.com
    sudo pear channel-discover hamcrest.googlecode.com/svn/pear
    sudo pear install --alldeps deepend/Mockery

**Git / GitHub**

The git repository hosts the development version in its master branch. You can
install this using Composer by referencing dev-master as your preferred version
in your project's composer.json file as the earlier example shows.

You may also install this development version using PEAR:

    git clone git://github.com/padraic/mockery.git
    cd mockery
    sudo pear channel-discover hamcrest.googlecode.com/svn/pear
    sudo pear install --alldeps package.xml

The above processes will install both Mockery and Hamcrest.
While omitting Hamcrest will not break Mockery, Hamcrest is recommended
as it adds a wider variety of functionality for argument matching.

**Unit Testing**

To run the unit tests for Mockery, clone the git repository, download Composer (i.e. composer.phar) using the instructions at [http://getcomposer.org/download/](http://getcomposer.org/download/) and run the following Composer command from the root directory of Mockery:

    php /path/to/composer.phar install --dev

This will install the required Hamcrest dev dependency and create the autoload files required by the unit tests. Navigate to the "tests" directory and run the phpunit command as normal. With a wee bit of luck, there will be no failed tests!

Upgrading to 0.8.*
------------------

Since the release of 0.8.0 the following behaviours were altered:

1. The shouldIgnoreMissing() behaviour optionally applied to mock objects returned an instance of
\Mockery\Undefined when methods called did not match a known expectation. Since 0.8.0, this behaviour
was switched to returning NULL instead. You can restore the 0.7.2 behavour by using the following:

```PHP
$mock = \Mockery::mock('stdClass')->shouldIgnoreMissing()->asUndefined();
```

Simple Example
--------------

Imagine we have a Temperature class which samples the temperature of a locale
before reporting an average temperature. The data could come from a web service
or any other data source, but we do not have such a class at present. We can,
however, assume some basic interactions with such a class based on its interaction
with the Temperature class.

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
When writing a test for the Temperature class, we can now substitute a mock
object for the real service which allows us to test the behaviour of the
Temperature class without actually needing a concrete service instance.

Note: PHPUnit integration (see below) can remove the need for a tearDown() method.

```PHP
use \Mockery as m;

class TemperatureTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
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

We'll cover the API in greater detail below.

PHPUnit Integration
-------------------

Mockery was designed as a simple to use standalone mock object framework, so
its need for integration with any testing framework is entirely optional.
To integrate Mockery, you just need to define a tearDown() method for your
tests containing the following (you may use a shorter \Mockery namespace alias):

```PHP
public function tearDown() {
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

    public function tearDown() {
        m::close();
    }
}
```

Mockery ships with an autoloader so you don't need to litter your tests with
require_once() calls. To use it, ensure Mockery is on your include_path and add
the following to your test suite's Bootstrap.php or TestHelper.php file:

```PHP
require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';
$loader = new \Mockery\Loader;
$loader->register();
```

If you are using Composer, you can simiplify this to just including the Composer generated autoloader file:

```PHP
require __DIR__ . '/../vendor/autoload.php'; // assuming vendor is one directory up
```

(Note: Prior to Hamcrest 1.0.0, the Hamcrest.php file name had a small "h", i.e. hamcrest.php. If upgrading Hamcrest to 1.0.0 remember to check the file name is updated for all your projects.)

To integrate Mockery into PHPUnit and avoid having to call the close method and
have Mockery remove itself from code coverage reports, use this in you suite:

```PHP
//Create Suite
$suite = new PHPUnit_Framework_TestSuite();

//Create a result listener or add it
$result = new PHPUnit_Framework_TestResult();
$result->addListener(new \Mockery\Adapter\Phpunit\TestListener());

// Run the tests.
$suite->run($result);
```

If you are using PHPUnit's XML configuration approach, you can include the following to load the TestListener:
``` XML
<listeners>
    <listener class="\Mockery\Adapter\Phpunit\TestListener"></listener>
</listeners>
```

Make sure Composer's or Mockery's autoloader is present in the bootstrap file or you will need to also define a
"file" attribute pointing to the file of the above TestListener class.

### Warning: PHPUnit running tests in separate processes

PHPUnit provides a functionnality that allows [tests to run in a separated process]
(http://phpunit.de/manual/3.7/en/appendixes.annotations.html#appendixes.annotations.runTestsInSeparateProcesses),
to ensure better isolation. Mockery verifies the mocks expectations using the
`Mockery::close` method, and provides a PHPUnit listener, that automatically
calls this method for you after every test.

However, this listener is not called in the right process when using PHPUnit's process
isolation, resulting in expectations that might not be respected, but without raising
any `Mockery\Exception`. To avoid this, you cannot rely on the supplied Mockery PHPUnit
`TestListener`, and you need to explicitely calls `Mockery::close`. The easiest solution
to include this call in the `tearDown()` method, as explained previously.

Quick Reference
---------------

Mockery implements a shorthand API when creating a mock. Here's a sampling
of the possible startup methods.

```PHP
$mock = \Mockery::mock('foo');
```

Creates a mock object named foo. In this case, foo is a name (not necessarily
a class name) used as a simple identifier when raising exceptions. This creates
a mock object of type \Mockery\Mock and is the loosest form of mock possible.

```PHP
$mock = \Mockery::mock(array('foo'=>1,'bar'=>2));
```

Creates an mock object named unknown since we passed no name. However we did
pass an expectation array, a quick method of setting up methods to expect with
their return values.

```PHP
$mock = \Mockery::mock('foo', array('foo'=>1,'bar'=>2));
```

Similar to the previous examples and all examples going forward, expectation arrays
can be passed for all mock objects as the second parameter to mock().

```PHP
$mock = \Mockery::mock('foo', function($mock) {
    $mock->shouldReceive(method_name);
});
```

In addition to expectation arrays, you can also pass in a closure which contains
reusable expectations. This can be passed as the second parameter, or as the third
parameter if partnered with an expectation array. This is one method for creating
reusable mock expectations.

```PHP
$mock = \Mockery::mock('stdClass');
```

Creates a mock identical to a named mock, except the name is an actual class
name. Creates a simple mock as previous examples show, except the mock
object will inherit the class type (via inheritance), i.e. it will pass type hints
or instanceof evaluations for stdClass. Useful where a mock object must be of a specific
type.

```PHP
$mock = \Mockery::mock('FooInterface');
```

You can create mock objects based on any concrete class, abstract class or
even an interface. Again, the primary purpose is to ensure the mock object
inherits a specific type for type hinting. There is an exception in that classes
marked final, or with methods marked final, cannot be mocked fully. In these cases
a partial mock (explained later) must be utilised.

```PHP
$mock = \Mockery::mock('alias:MyNamespace\MyClass');
```

Prefixing the valid name of a class (which is NOT currently loaded) with "alias:"
will generate an "alias mock". Alias mocks create a class alias with the given
classname to stdClass and are generally used to enable the mocking of public
static methods. Expectations set on the new mock object which refer to static
methods will be used by all static calls to this class.

```PHP
$mock = \Mockery::mock('overload:MyNamespace\MyClass');
```

Prefixing the valid name of a class (which is NOT currently loaded) with "overload:" will
generate an alias mock (as with "alias:") except that created new instances of that
class will import any expectations set on the origin mock ($mock). The origin
mock is never verified since it's used an expectation store for new instances. For this
purpose I used the term "instance mock" to differentiate it from the simpler "alias mock".

Note: Using alias/instance mocks across more than one test will generate a fatal error since
you can't have two classes of the same name. To avoid this, run each test of this
kind in a separate PHP process (which is supported out of the box by both
PHPUnit and PHPT).

```PHP
$mock = \Mockery::mock('stdClass, MyInterface1, MyInterface2');
```

The first argument can also accept a list of interfaces that the mock object must
implement, optionally including no more than one existing class to be based on. The
class name doesn't need to be the first member of the list but it's a friendly
convention to use for readability. All subsequent arguments remain unchanged from
previous examples.

If the given class does not exist, you must define and include it beforehand or a
\Mockery\Exception will be thrown.

```PHP
$mock = \Mockery::mock('MyNamespace\MyClass[foo,bar]');
```

The syntax above tells Mockery to partially mock the MyNamespace\MyClass class,
by mocking the foo() and bar() methods only. Any other method will be not be
overridden by Mockery. This traditional form of "partial mock" can be applied to any class
or abstract class (e.g. mocking abstract methods where a concrete implementation
does not exist yet). If you attempt to partial mock a method marked final, it will
actually be ignored in that instance leaving the final method untouched. This is
necessary since mocking of final methods is, by definition in PHP, impossible.

Please refer to [Creating Partial Mocks](#creating-partial-mocks) for a detailed
explanation on how to create Partial Mocks in Mockery.

```PHP
$mock = \Mockery::mock("MyNamespace\MyClass[foo]", array($arg1, $arg2));
```

If Mockery encounters an indexed array as the second or third argument, it will
assume they are constructor parameters and pass them when constructing the mock
object. The syntax above will create a new partial mock, particularly useful if
method `bar` calls method `foo` internally with `$this->foo()`.

```PHP
$mock = \Mockery::mock(new Foo);
```

Passing any real object into Mockery will create a Proxied Partial Mock. This
can be useful if real partials are impossible, e.g. a final class or class where
you absolutely must override a method marked final. Since you can already create
a concrete object, so all we need to do is selectively
override a subset of existing methods (or add non-existing methods!) for
our expectations.

A little revision: All mock methods accept the class, object or alias name to be
mocked as the first parameter. The second parameter can be an expectation array
of methods and their return values, or an expectation closure (which can be the
third param if used in conjunction with an expectation array).

```PHP
\Mockery::self()
```

At times, you will discover that expectations on a mock include methods which need
to return the same mock object (e.g. a common case when designing a Domain Specific
Language (DSL) such as the one Mockery itself uses!). To facilitate this, calling
\Mockery::self() will always return the last Mock Object created by calling
\Mockery::mock(). For example:

```PHP
$mock = \Mockery::mock('BazIterator')
    ->shouldReceive('next')
    ->andReturn(\Mockery::self())
    ->mock();
```

The above class being mocked, as the next() method suggests, is an iterator. In
many cases, you can replace all the iterated elements (since they are the same type
many times) with just the one mock object which is programmed to act as discrete
iterated elements.

### Behaviour Modifiers

When creating a mock object, you may wish to use some commonly preferred behaviours
that are not the default in Mockery.

```PHP
\Mockery::mock('MyClass')->shouldIgnoreMissing()
```

The use of the shouldIgnoreMissing() behaviour modifier will label this mock object
as a Passive Mock. In such a mock object, calls to methods which are not covered by
expectations will return NULL
instead of the usual complaining about there being no expectation matching the call.

You can optionally prefer to return an object of type \Mockery\Undefined (i.e.
a null object) (which was the 0.7.2 behaviour) by using an additional modifier:

```PHP
\Mockery::mock('MyClass')->shouldIgnoreMissing()->asUndefined()
```

The returned object is nothing more than a placeholder so if, by some act of fate,
it's erroneously used somewhere it shouldn't it will likely not pass a logic check.

```PHP
\Mockery::mock('MyClass')->makePartial()
```

also

```PHP
\Mockery::mock('MyClass')->shouldDeferMissing()
```

Known as a Passive Partial Mock (not to be confused with real partial mock objects
discussed later), this form of mock object will defer all methods not subject to
an expectation to the parent class of the mock, i.e. MyClass. Whereas the previous
shouldIgnoreMissing() returned NULL, this behaviour simply
calls the parent's matching method.

Expectation Declarations
------------------------

Once you have created a mock object, you'll often want to start defining how
exactly it should behave (and how it should be called). This is where the
Mockery expectation declarations take over.

```PHP
shouldReceive(method_name)
```

Declares that the mock expects a call to the given method name. This is the
starting expectation upon which all other expectations and constraints are
appended.

```PHP
shouldReceive(method1, method2, ...)
```

Declares a number of expected method calls, all of which will adopt any chained
expectations or constraints.

```PHP
shouldReceive(array('method1'=>1, 'method2'=>2, ...))
```

Declares a number of expected calls but also their return values. All will
adopt any additional chained expectations or constraints.

```PHP
shouldReceive(closure)
```

Creates a mock object (only from a partial mock) which is used to create a mock
object recorder. The recorder is a simple proxy to the original object passed
in for mocking. This is passed to the closure, which may run it through a set of
operations which are recorded as expectations on the partial mock. A simple
use case is automatically recording expectations based on an existing usage
(e.g. during refactoring). See examples in a later section.

```PHP
with(arg1, arg2, ...) / withArgs(array(arg1, arg2, ...))
```

Adds a constraint that this expectation only applies to method calls which
match the expected argument list. You can add a lot more flexibility to argument
matching using the built in matcher classes (see later). For example,
\Mockery::any() matches any argument passed to that position in the with()
parameter list. Mockery also allows Hamcrest library matchers - for example, the
Hamcrest function anything() is equivalent to \Mockery::any().

It's important to note that this means all expectations attached only apply
to the given method when it is called with these exact arguments. This allows for
setting up differing expectations based on the arguments provided to expected calls.

```PHP
withAnyArgs()
```

Declares that this expectation matches a method call regardless of what arguments
are passed. This is set by default unless otherwise specified.

```PHP
withNoArgs()
```

Declares this expectation matches method calls with zero arguments.

```PHP
andReturn(value)
```

Sets a value to be returned from the expected method call.

```PHP
andReturn(value1, value2, ...)
```

Sets up a sequence of return values or closures. For example, the first call will return
value1 and the second value2. Note that all subsequent calls to a mocked method
will always return the final value (or the only value) given to this declaration.

```PHP
andReturnNull() / andReturn([NULL])
```

Both of the above options are primarily for communication to test readers. They mark the
mock object method call as returning NULL or nothing.

```PHP
andReturnValues(array)
```

Alternative syntax for andReturn() that accepts a simple array instead of a list of parameters.
The order of return is determined by the numerical index of the given array with the last array
member being return on all calls once previous return values are exhausted.

```PHP
andReturnUsing(closure, ...)
```

Sets a closure (anonymous function) to be called with the arguments passed to
the method. The return value from the closure is then returned. Useful for some
dynamic processing of arguments into related concrete results. Closures can
queued by passing them as extra parameters as for andReturn(). Note that you
cannot currently mix andReturnUsing() with andReturn().

```PHP
andThrow(Exception)
```

Declares that this method will throw the given Exception object when called.

```PHP
andThrow(exception_name, message)
```

Rather than an object, you can pass in the Exception class and message to
use when throwing an Exception from the mocked method.

```PHP
andSet(name, value1) / set(name, value1)
```

Used with an expectation so that when a matching method is called, one
can also cause a mock object's public property to be set to a specified value.

```PHP
passthru()
```

Tells the expectation to bypass a return queue and instead call the real method
of the class that was mocked and return the result. Basically, it allows
expectation matching and call count validation to be applied against real methods
while still calling the real class method with the expected arguments.

```PHP
zeroOrMoreTimes()
```

Declares that the expected method may be called zero or more times. This is
the default for all methods unless otherwise set.

```PHP
once()
```

Declares that the expected method may only be called once. Like all other
call count constraints, it will throw a \Mockery\CountValidator\Exception
if breached and can be modified by the atLeast() and atMost() constraints.

```PHP
twice()
```

Declares that the expected method may only be called twice.

```PHP
times(n)
```

Declares that the expected method may only be called n times.

```PHP
never()
```

Declares that the expected method may never be called. Ever!

```PHP
atLeast()
```

Adds a minimum modifier to the next call count expectation. Thus
atLeast()->times(3) means the call must be called at least three times (given
matching method args) but never less than three times.

```PHP
atMost()
```
Adds a maximum modifier to the next call count expectation. Thus
atMost()->times(3) means the call must be called no more than three times. This
also means no calls are acceptable.

```PHP
between(min, max)
```

Sets an expected range of call counts. This is actually identical to using
atLeast()->times(min)->atMost()->times(max) but is provided as a shorthand.
It may be followed by a times() call with no parameter to preserve the
APIs natural language readability.

```PHP
ordered()
```

Declares that this method is expected to be called in a specific order in
relation to similarly marked methods. The order is dictated by the order in
which this modifier is actually used when setting up mocks.

```PHP
ordered(group)
```

Declares the method as belonging to an order group (which can be named or
numbered). Methods within a group can be called in any order, but the ordered
calls from outside the group are ordered in relation to the group, i.e. you can
set up so that method1 is called before group1 which is in turn called before
method 2.

```PHP
globally()
```

When called prior to ordered() or ordered(group), it declares this ordering to
apply across all mock objects (not just the current mock). This allows for dictating
order expectations across multiple mocks.

```PHP
byDefault()
```

Marks an expectation as a default. Default expectations are applied unless
a non-default expectation is created. These later expectations immediately
replace the previously defined default. This is useful so you can setup default
mocks in your unit test setup() and later tweak them in specific tests as
needed.

```PHP
getMock()
```

Returns the current mock object from an expectation chain. Useful where
you prefer to keep mock setups as a single statement, e.g.

```PHP
$mock = \Mockery::mock('foo')->shouldReceive('foo')->andReturn(1)->getMock();
```

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

Mockery's generic matchers do not cover all possibilities but offers optional
support for the Hamcrest library of matchers. Hamcrest is a PHP port of the
similarly named Java library (which has been ported also to Python, Erlang, etc).
I strongly recommend using Hamcrest since Mockery simply does not need to duplicate
Hamcrest's already impressive utility which itself promotes a natural English DSL.

The example below show Mockery matchers and their Hamcrest equivalent. Hamcrest uses
functions (no namespacing).

Here's a sample of the possibilities.

```PHP
with(1)
```

Matches the integer 1. This passes the === test (identical). It does facilitate
a less strict == check (equals) where the string '1' would also match the
argument.

```PHP
with(\Mockery::any()) OR with(anything())
```

Matches any argument. Basically, anything and everything passed in this argument
slot is passed unconstrained.

```PHP
with(\Mockery::type('resource')) OR with(resourceValue()) OR with(typeOf('resource'))
```

Matches any resource, i.e. returns true from an is_resource() call. The Type
matcher accepts any string which can be attached to "is_" to form a valid
type check. For example, \Mockery::type('float') or Hamcrest's floatValue() and
typeOf('float') checks using is_float(), and \Mockery::type('callable') or Hamcrest's
callable() uses is_callable().

The Type matcher also accepts a class or interface name to be used in an instanceof
evaluation of the actual argument (similarly Hamcrest uses anInstanceOf()).

You may find a full list of the available type checkers at
http://www.php.net/manual/en/ref.var.php or browse Hamcrest's function list at
http://code.google.com/p/hamcrest/source/browse/trunk/hamcrest-php/hamcrest/Hamcrest.php.

```PHP
with(\Mockery::on(closure))
```

The On matcher accepts a closure (anonymous function) to which the actual argument
will be passed. If the closure evaluates to (i.e. returns) boolean TRUE then
the argument is assumed to have matched the expectation. This is invaluable
where your argument expectation is a bit too complex for or simply not
implemented in the current default matchers.

There is no Hamcrest version of this functionality.

```PHP
with('/^foo/') OR with(matchesPattern('/^foo/'))
```

The argument declarator also assumes any given string may be a regular
expression to be used against actual arguments when matching. The regex option
is only used when a) there is no === or == match and b) when the regex
is verified to be a valid regex (i.e. does not return false from preg_match()).
If the regex detection doesn't suit your tastes, Hamcrest offers the more
explicit matchesPattern() function.

```PHP
with(\Mockery::ducktype('foo', 'bar'))
```

The Ducktype matcher is an alternative to matching by class type. It simply
matches any argument which is an object containing the provided list
of methods to call.

There is no Hamcrest version of this functionality.

```PHP
with(\Mockery::mustBe(2)) OR with(identicalTo(2))
```

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

```PHP
with(\Mockery::not(2)) OR with(not(2))
```

The Not matcher matches any argument which is not equal or identical to the
matcher's parameter.

```PHP
with(\Mockery::anyOf(1, 2)) OR with(anyOf(1,2))
```

Matches any argument which equals any one of the given parameters.

```PHP
with(\Mockery::notAnyOf(1, 2))
```

Matches any argument which is not equal or identical to any of the given
parameters.

There is no Hamcrest version of this functionality.

```PHP
with(\Mockery::subset(array(0=>'foo')))
```

Matches any argument which is any array containing the given array subset. This
enforces both key naming and values, i.e. both the key and value of each
actual element is compared.

There is no Hamcrest version of this functionality, though Hamcrest can check a
single entry using hasEntry() or hasKeyValuePair().

```PHP
with(\Mockery::contains(value1, value2))
```

Matches any argument which is an array containing the listed values. The naming
of keys is ignored.

```PHP
with(\Mockery::hasKey(key));
```

Matches any argument which is an array containing the given key name.

```PHP
with(\Mockery::hasValue(value));
```

Matches any argument which is an array containing the given value.


Creating Partial Mocks
----------------------

Partial mocks are useful when you only need to mock several methods of
an object leaving the remainder free to respond to calls normally (i.e.
as implemented). Mockery implements three distinct strategies for creating
partials. Each has specific advantages and disadvantages so which strategy
you use will depend on your own preferences and the source code in need
of mocking.

1. Traditional Partial Mock
2. Passive Partial Mock
3. Proxied Partial Mock

### Traditional Partial Mock

A traditional partial mock defined ahead of time which methods of a class
are to be mocked and which are to left unmocked (i.e. callable as normal).
The syntax for creating traditional mocks is:

```PHP
$mock = \Mockery::mock('MyClass[foo,bar]');
```

In the above example, the foo() and bar() methods of MyClass will be
mocked but no other MyClass methods are touched. You will need to define
expectations for the foo() and bar() methods to dictate their mocked behaviour.

Don't forget that you can pass in constructor arguments since unmocked
methods may rely on those!

```PHP
$mock = \Mockery::mock("MyNamespace\MyClass[foo]", array($arg1, $arg2));
```

### Passive Partial Mock

A passive partial mock is more of a default state of being.

```PHP
$mock = \Mockery::mock('MyClass')->makePartial();
```

In a passive partial, we assume that all methods will simply defer to
the parent class (MyClass) original methods unless a method call
matches a known expectation. If you have no matching expectation for
a specific method call, that call is deferred to the class being
mocked. Since the division between mocked and unmocked calls depends
entirely on the expectations you define, there is no need to define
which methods to mock in advance. The makePartial() method is identical to the
original shouldDeferMissing() method which first introduced this Partial Mock
type.

### Proxied Partial Mock

A proxied partial mock is a partial of last resort. You may encounter
a class which is simply not capable of being mocked because it has
been marked as final. Similarly, you may find a class with methods
marked as final. In such a scenario, we cannot simply extend the
class and override methods to mock - we need to get creative.

```PHP
$mock = \Mockery::mock(new MyClass);
```

Yes, the new mock is a Proxy. It intercepts calls and reroutes them to
the proxied object (which you construct and pass in) for methods which
are not subject to any expectations. Indirectly, this allows you to
mock methods marked final since the Proxy is not subject to those
limitations. The tradeoff should be obvious - a proxied partial will
fail any typehint checks for the class being mocked since it cannot
extend that class.

#### Special Internal Cases

All mock objects, with the exception of Proxied Partials, allow you to make any
expectation call the underlying real class method using the passthru() expectation
call. This will return values from the real call and bypass any mocked return queue
(which can simply be omitted obviously).

There is a fourth kind of partial mock reserved for internal use. This is automatically
generated when you attempt to mock a class containing methods marked final. Since we
cannot override such methods, they are simply left unmocked. Typically, you don't need
to worry about this but if you really really must mock a final method, the only possible
means is through a Proxied Partial Mock. SplFileInfo, for example, is a common class subject
to this form of automatic internal partial since it contains public final methods used
internally.

Detecting Mock Objects
----------------------

Users may find it useful to check whether a given object is a real object or a simulated
Mock Object. All Mockery mocks implement the \Mockery\MockInterface interface which can
be used in a type check.

```PHP
assert($mightBeMocked instanceof \Mockery\MockInterface);
```

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

Mocking Public Properties
-------------------------

Mockery allows you to mock properties in several ways. The simplest is that
you can simply set a public property and value on any mock object. The second
is that you can use the expectation methods set() and andSet() to set property
values if that expectation is ever met.

You should note that, in general, Mockery does not support mocking any magic
methods since these are generally not considered a public API (and besides they
are a PITA to differentiate when you badly need them for mocking!). So please
mock virtual properties (those relying on __get and __set) as if they were
actually declared on the class.

Mocking Public Static Methods
-----------------------------

Static methods are not called on real objects, so normal mock objects can't mock
them. Mockery supports class aliased mocks, mocks representing a class name which
would normally be loaded (via autoloading or a require statement) in the system
under test. These aliases block that loading (unless via a require statement - so please
use autoloading!) and allow Mockery to intercept static method calls and add
expectations for them.

Generating Mock Objects Upon Instantiation (Instance Mocking)
-------------------------------------------------------------


Instance mocking means that a statement like:

```PHP
$obj = new \MyNamespace\Foo;
```

...will actually generate a mock object. This is done by replacing the real class
with an instance mock (similar to an alias mock), as with mocking public methods.
The alias will import its
expectations from the original mock of that type (note that the original is never
verified and should be ignored after its expectations are setup). This lets you
intercept instantiation where you can't simply inject a replacement object.

As before, this does not prevent a require statement from including the real
class and triggering a fatal PHP error. It's intended for use where autoloading
is the primary class loading mechanism.

Preserving Pass-By-Reference Method Parameter Behaviour
-------------------------------------------------------

PHP Class method may accept parameters by reference. In this case, changes made
to the parameter (a reference to the original variable passed to the method) are
reflected in the original variable. A simple example:
```PHP
class Foo {
    public function bar(&$a) {
        $a++;
    }
}

$baz = 1;
$foo = new Foo;
$foo->bar($baz);

echo $baz; // will echo the integer 2
```

In the example above, the variable $baz is passed by reference to Foo::bar()
(notice the "&" symbol in front of the parameter?).
Any change bar() makes to the parameter reference is reflected in the original
variable, $baz.

Mockery 0.7+ handles references correctly for all methods where it can analyse the
parameter (using Reflection) to see if it is passed by reference. To mock how a
reference is manipulated by the class method, you can use a closure argument
matcher to manipulate it, i.e. \Mockery::on() - see Argument Validation section
above.

There is an exception for internal PHP classes where Mockery cannot analyse
method parameters using Reflection (a limitation in PHP). To work around this,
you can explicitly declare method parameters for an internal class using
/Mockery/Configuration::setInternalClassMethodParamMap().

Here's an example using MongoCollection::insert(). MongoCollection is an internal
class offered by the mongo extension from PECL. Its insert() method accepts an array
of data as the first parameter, and an optional options array as the second
parameter. The original data array is updated (i.e. when a insert() pass-by-reference
parameter) to include a new "_id" field. We can mock this behaviour using
a configured parameter map (to tell Mockery to expect a pass by reference parameter)
and a Closure attached to the expected method parameter to be updated.

Here's a PHPUnit unit test verifying that this pass-by-reference behaviour is preserved:

```PHP
public function testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs()
{
    \Mockery::getConfiguration()->setInternalClassMethodParamMap(
        'MongoCollection',
        'insert',
        array('&$data', '$options = array()')
    );
    $m = \Mockery::mock('MongoCollection');
    $m->shouldReceive('insert')->with(
        \Mockery::on(function(&$data) {
            if (!is_array($data)) return false;
            $data['_id'] = 123;
            return true;
        }),
        \Mockery::any()
    );
    $data = array('a'=>1,'b'=>2);
    $m->insert($data);
    $this->assertTrue(isset($data['_id']));
    $this->assertEquals(123, $data['_id']);
    \Mockery::resetContainer();
}
```

Mocking Demeter Chains And Fluent Interfaces
--------------------------------------------

Both of these terms refer to the growing practice of invoking statements
similar to:

```PHP
$object->foo()->bar()->zebra()->alpha()->selfDestruct();
```

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

```PHP
$mock = \Mockery::mock('CaptainsConsole');
$mock->shouldReceive('foo->bar->zebra->alpha->selfDestruct')->andReturn('Ten!');
```

The above expectation can follow any previously seen format or expectation, except
that the method name is simply the string of all expected chain calls separated
by "->". Mockery will automatically setup the chain of expected calls with
its final return values, regardless of whatever intermediary object might be
used in the real implementation.

Arguments to all members of the chain (except the final call) are ignored in
this process.

Mockery Exceptions
------------------

Mockery throws three types of exceptions when it cannot verify a mock object.

1. \Mockery\Exception\InvalidCountException
2. \Mockery\Exception\InvalidOrderException
3. \Mockery\Exception\NoMatchingExpectationException

You can capture any of these exceptions in a try...catch block to query them for
specific information which is also passed along in the exception message but is provided
separately from getters should they
be useful when logging or reformatting output.

### \Mockery\Exception\InvalidCountException

The exception class is used when a method is called too many (or too few) times
and offers the following methods:

+ getMock() - return actual mock object
+ getMockName() - return the name of the mock object
+ getMethodName() - return the name of the method the failing expectation is attached to
+ getExpectedCount() - return expected calls
+ getExpectedCountComparative() - returns a string, e.g. "<=" used to compare to actual count
+ getActualCount() - return actual calls made with given argument constraints

### \Mockery\Exception\InvalidOrderException

The exception class is used when a method is called outside the expected order set using the
ordered() and globally() expectation modifiers. It offers the following methods:

+ getMock() - return actual mock object
+ getMockName() - return the name of the mock object
+ getMethodName() - return the name of the method the failing expectation is attached to
+ getExpectedOrder() - returns an integer represented the expected index for which this call was expected
+ getActualOrder() - return the actual index at which this method call occured.

### \Mockery\Exception\NoMatchingExpectationException

The exception class is used when a method call does not match any known expectation.
All expectations are uniquely identified in a mock object by the method name and the list
of expected arguments. You can disable this exception and opt for returning NULL from all
unexpected method calls by using the earlier mentioned shouldIgnoreMissing() behaviour
modifier.
This exception class offers the following methods:

+ getMock() - return actual mock object
+ getMockName() - return the name of the mock object
+ getMethodName() - return the name of the method the failing expectation is attached to
+ getActualArguments() - return actual arguments used to search for a matching expectation

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

```PHP
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
```

Here's the test case showing the recording:

```PHP
class SubjectUserTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
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
```

After the \Mockery::close() call in tearDown() validates the mock object, we
should have zero exceptions if NewSubjectUser acted on Subject in a similar way
to SubjectUser. By default the order of calls are not enforced, and loose argument
matching is enabled, i.e. arguments may be equal (==) but not necessarily identical
(===).

If you wished to be more strict, for example ensuring the order of calls
and the final call counts were identical, or ensuring arguments are completely
identical, you can invoke the recorder's strict mode from the closure block, e.g.

```PHP
$mock->shouldExpect(function ($subject) {
    $subject->shouldBeStrict();
    $user = new SubjectUser;
    $user->use($subject);
});
```

Dealing with Final Classes/Methods
----------------------------------

One of the primary restrictions of mock objects in PHP, is that mocking classes
or methods marked final is hard. The final keyword prevents methods so marked
from being replaced in subclasses (subclassing is how mock objects can inherit
the type of the class or object being mocked.

The simplest solution is not to mark classes or methods as final!

However, in a compromise between mocking functionality and type safety, Mockery
does allow creating "proxy mocks" from classes marked final, or from classes with
methods marked final. This offers all the usual mock object goodness but the
resulting mock will not inherit the class type of the object being mocked, i.e.
it will not pass any instanceof comparison.

You can create a proxy mock by passing the instantiated object you wish to mock
into \Mockery::mock(), i.e. Mockery will then generate a Proxy to the real object
and selectively intercept method calls for the purposes of setting and
meeting expectations.

Mockery Global Configuration
----------------------------

To allow for a degree of fine-tuning, Mockery utilises a singleton configuration
object to store a small subset of core behaviours. The three currently present
include:

* Option to allow/disallow the mocking of methods which do not actually exist
* Option to allow/disallow the existence of expectations which are never fulfilled (i.e. unused)
* Setter/Getter for added a parameter map for internal PHP class methods (Reflection cannot detect these automatically)

By default, the first two behaviours are enabled. Of course, there are situations where
this can lead to unintended consequences. The mocking of non-existent methods
may allow mocks based on real classes/objects to fall out of sync with the
actual implementations, especially when some degree of integration testing (testing
of object wiring) is not being performed. Allowing unfulfilled expectations means
unnecessary mock expectations go unnoticed, cluttering up test code, and
potentially confusing test readers.

You may allow or disallow these behaviours (whether for whole test suites or just
select tests) by using one or both of the following two calls:

```PHP
\Mockery::getConfiguration()->allowMockingNonExistentMethods(bool);
\Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(bool);
```

Passing a true allows the behaviour, false disallows it. Both take effect
immediately until switched back. In both cases, if either
behaviour is detected when not allowed, it will result in an Exception being
thrown at that point. Note that disallowing these behaviours should be carefully
considered since they necessarily remove at least some of Mockery's flexibility.

The other two methods are:

```PHP
\Mockery::getConfiguration()->setInternalClassMethodParamMap($class, $method, array $paramMap)
\Mockery::getConfiguration()->getInternalClassMethodParamMap($class, $method)
```

These are used to define parameters (i.e. the signature string of each) for the
methods of internal PHP classes (e.g. SPL, or PECL extension classes like
ext/mongo's MongoCollection. Reflection cannot analyse the parameters of internal
classes. Most of the time, you never need to do this. It's mainly needed where an
internal class method uses pass-by-reference for a parameter - you MUST in such
cases ensure the parameter signature includes the "&" symbol correctly as Mockery
won't correctly add it automatically for internal classes.

Reserved Method Names
---------------------

As you may have noticed, Mockery uses a number of methods called directly on
all mock objects, for example shouldReceive(). Such methods are necessary
in order to setup expectations on the given mock, and so they cannot be
implemented on the classes or objects being mocked without creating a method
name collision (reported as a PHP fatal error). The methods reserved by Mockery are:

* shouldReceive()
* shouldBeStrict()

In addition, all mocks utilise a set of added methods and protected properties
which cannot exist on the class or object being mocked. These are far less likely
to cause collisions. All properties are prefixed with "_mockery" and all method
names with "mockery_".

PHP Magic Methods
-----------------

PHP magic methods which are prefixed with a double underscore, e.g. _set(), pose
a particular problem in mocking and unit testing in general. It is strongly
recommended that unit tests and mock objects do not directly refer to magic
methods. Instead, refer only to the virtual methods and properties these magic
methods simulate.

Following this piece of advice will ensure you are testing the real API of classes
and also ensures there is no conflict should Mockery override these magic methods,
which it will inevitably do in order to support its role in intercepting method
calls and properties.

Gotchas!
--------

Mocking objects in PHP has its limitations and gotchas. Some functionality can't
be mocked or can't be mocked YET! If you locate such a circumstance, please please
(pretty please with sugar on top) create a new issue on GitHub so it can be
documented and resolved where possible. Here is a list to note:

1. Classes containing public __wakeup methods can be mocked but the mocked __wakeup
method will perform no actions and cannot have expectations set for it. This is
necessary since Mockery must serialize and unserialize objects to avoid some
__construct() insanity and attempting to mock a __wakeup method as normal leads
to a BadMethodCallException been thrown.

2. Classes using non-real methods, i.e. where a method call triggers a __call
method, will throw an exception that the non-real method does not exist unless
you first define at least one expectation (a simple shouldReceive() call would
suffice). This is necessary since there is no other way for Mockery to be
aware of the method name.

3. Mockery has two scenarios where real classes are replaced: Instance mocks and
alias mocks. Both will generate PHP fatal errors if the real class is loaded,
usually via a require or include statement. Only use these two mock types where
autoloading is in place and where classes are not explicitly loaded on a per-file
basis using require(), require_once(), etc.

4. Internal PHP classes are not entirely capable of being fully analysed using
Reflection. For example, Reflection cannot reveal details of expected parameters
to the methods of such internal classes. As a result, there will be problems
where a method parameter is defined to accept a value by reference (Mockery
cannot detect this condition and will assume a pass by value on scalars and
arrays). If references as internal class method parameters are needed, you
should use the \Mockery\Configuration::setInternalClassMethodParamMap() method.

The gotchas noted above are largely down to PHP's architecture and are assumed
to be unavoidable. But - if you figure out a solution (or a better one than what
may exist), let me know!

Quick Examples
--------------

Create a mock object to return a sequence of values from a set of method calls.

```PHP
class SimpleTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
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

    public function tearDown()
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

    public function tearDown()
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
```

Expect all queries to be executed before any updates.

```PHP
use \Mockery as m;

class DbTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
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
```

Create a mock object where all queries occur after startup, but before finish, and
where queries are expected with several different params.

```PHP
use \Mockery as m;

class DbTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
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
