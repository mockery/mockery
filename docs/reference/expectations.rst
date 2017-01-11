.. index::
    single: Expectations

Expectation Declarations
========================

.. note::

    In order for your expectations to work you MUST call ``Mockery::close()``, preferably in a callback method such as ``tearDown`` or ``_before`` (depending on whether or not you're integrating Mockery with another framework). This static call cleans up the Mockery container used by the current test, and run any verification tasks needed for your expectations.
    
Once you have created a mock object, you'll often want to start defining how
exactly it should behave (and how it should be called). This is where the
Mockery expectation declarations take over.

.. code-block:: php

    shouldReceive(method_name)

Declares that the mock expects a call to the given method name. This is the
starting expectation upon which all other expectations and constraints are
appended.

.. code-block:: php

    shouldReceive(method1, method2, ...)

Declares a number of expected method calls, all of which will adopt any
chained expectations or constraints.

.. code-block:: php

    shouldReceive(array('method1'=>1, 'method2'=>2, ...))

Declares a number of expected calls but also their return values. All will
adopt any additional chained expectations or constraints.

.. code-block:: php

    shouldReceive(closure)

Creates a mock object (only from a partial mock) which is used to create a
mock object recorder. The recorder is a simple proxy to the original object
passed in for mocking. This is passed to the closure, which may run it through
a set of operations which are recorded as expectations on the partial mock. A
simple use case is automatically recording expectations based on an existing
usage (e.g. during refactoring). See examples in a later section.

.. code-block:: php

    shouldNotReceive(method_name)

Declares that the mock should not expect a call to the given method name. This
method is a convenience method for calling ``shouldReceive()->never()``.

.. code-block:: php

    with(arg1, arg2, ...) 
    withArgs(array(arg1, arg2, ...))

Adds a constraint that this expectation only applies to method calls which
match the expected argument list. You can add a lot more flexibility to
argument matching using the built in matcher classes (see later). For example,
``\Mockery::any()`` matches any argument passed to that position in the
``with()`` parameter list. Mockery also allows Hamcrest library matchers - for
example, the Hamcrest function ``anything()`` is equivalent to
``\Mockery::any()``.

It's important to note that this means all expectations attached only apply to
the given method when it is called with these exact arguments. This allows for
setting up differing expectations based on the arguments provided to expected
calls.

.. code-block:: php

    withArgs(closure)

Instead of providing a built-in matcher for each argument, you can provide a
closure that matches all passed arguments at once. The given closure receives
all the arguments passed in the call to the expected method. In this way, this
expectation only applies to method calls where passed arguments make the closure
evaluates to true.

.. code-block:: php

    withAnyArgs()

Declares that this expectation matches a method call regardless of what
arguments are passed. This is set by default unless otherwise specified.

.. code-block:: php

    withNoArgs()

Declares this expectation matches method calls with zero arguments.

.. code-block:: php

    andReturn(value)

Sets a value to be returned from the expected method call.

.. code-block:: php

    andReturn(value1, value2, ...)

Sets up a sequence of return values or closures. For example, the first call
will return value1 and the second value2. Note that all subsequent calls to a
mocked method will always return the final value (or the only value) given to
this declaration.

.. code-block:: php

    andReturnNull() / andReturn([NULL])

Both of the above options are primarily for communication to test readers.
They mark the mock object method call as returning ``null`` or nothing.

.. code-block:: php

    andReturnValues(array)

Alternative syntax for ``andReturn()`` that accepts a simple array instead of
a list of parameters. The order of return is determined by the numerical
index of the given array with the last array member being return on all calls
once previous return values are exhausted.

.. code-block:: php

    andReturnUsing(closure, ...)

Sets a closure (anonymous function) to be called with the arguments passed to
the method. The return value from the closure is then returned. Useful for
some dynamic processing of arguments into related concrete results. Closures
can queued by passing them as extra parameters as for ``andReturn()``.

.. code-block:: php

    andReturnSelf()
    
Set the return value to the mocked class name. Useful for mocking fluid interfaces.

.. note::

    You cannot currently mix ``andReturnUsing()`` with ``andReturn()``.

.. code-block:: php

    andThrow(Exception)

Declares that this method will throw the given ``Exception`` object when
called.

.. code-block:: php

    andThrow(exception_name, message)

Rather than an object, you can pass in the ``Exception`` class and message to
use when throwing an ``Exception`` from the mocked method.

.. code-block:: php

    andSet(name, value1) / set(name, value1)

Used with an expectation so that when a matching method is called, one can
also cause a mock object's public property to be set to a specified value.

.. code-block:: php

    passthru()

Tells the expectation to bypass a return queue and instead call the real
method of the class that was mocked and return the result. Basically, it
allows expectation matching and call count validation to be applied against
real methods while still calling the real class method with the expected
arguments.

.. code-block:: php

    zeroOrMoreTimes()

Declares that the expected method may be called zero or more times. This is
the default for all methods unless otherwise set.

.. code-block:: php

    once()

Declares that the expected method may only be called once. Like all other call
count constraints, it will throw a ``\Mockery\CountValidator\Exception`` if
breached and can be modified by the ``atLeast()`` and ``atMost()``
constraints.

.. code-block:: php

    twice()

Declares that the expected method may only be called twice.

.. code-block:: php

    times(n)

Declares that the expected method may only be called n times.

.. code-block:: php

    never()

Declares that the expected method may never be called. Ever!

.. code-block:: php

    atLeast()

Adds a minimum modifier to the next call count expectation. Thus
``atLeast()->times(3)`` means the call must be called at least three times
(given matching method args) but never less than three times.

.. code-block:: php

    atMost()

Adds a maximum modifier to the next call count expectation. Thus
``atMost()->times(3)`` means the call must be called no more than three times.
This also means no calls are acceptable.

.. code-block:: php

    between(min, max)

Sets an expected range of call counts. This is actually identical to using
``atLeast()->times(min)->atMost()->times(max)`` but is provided as a
shorthand.  It may be followed by a ``times()`` call with no parameter to
preserve the APIs natural language readability.

.. code-block:: php

    ordered()

Declares that this method is expected to be called in a specific order in
relation to similarly marked methods. The order is dictated by the order in
which this modifier is actually used when setting up mocks.

.. code-block:: php

    ordered(group)

Declares the method as belonging to an order group (which can be named or
numbered). Methods within a group can be called in any order, but the ordered
calls from outside the group are ordered in relation to the group, i.e. you
can set up so that method1 is called before group1 which is in turn called
before method 2.

.. code-block:: php

    globally()

When called prior to ``ordered()`` or ``ordered(group)``, it declares this
ordering to apply across all mock objects (not just the current mock). This
allows for dictating order expectations across multiple mocks.

.. code-block:: php

    byDefault()

Marks an expectation as a default. Default expectations are applied unless a
non-default expectation is created. These later expectations immediately
replace the previously defined default. This is useful so you can setup
default mocks in your unit test ``setup()`` and later tweak them in specific
tests as needed.

.. code-block:: php

    getMock()

Returns the current mock object from an expectation chain. Useful where you
prefer to keep mock setups as a single statement, e.g.

.. code-block:: php

    $mock = \Mockery::mock('foo')->shouldReceive('foo')->andReturn(1)->getMock();
