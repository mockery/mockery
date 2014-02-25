.. index::
    single: Reference; Quick Reference

Quick Reference
===============

Mockery implements a shorthand API when creating a mock. Here's a sampling of
the possible startup methods.

.. code-block:: php

    $mock = \Mockery::mock('foo');

Creates a mock object named "foo". In this case, "foo" is a name (not
necessarily a class name) used as a simple identifier when raising exceptions.
This creates a mock object of type ``\Mockery\Mock`` and is the loosest form
of mock possible.

.. code-block:: php

    $mock = \Mockery::mock(array('foo'=>1,'bar'=>2));

Creates an mock object named unknown since we passed no name. However we did
pass an expectation array, a quick method of setting up methods to expect with
their return values.

.. code-block:: php

    $mock = \Mockery::mock('foo', array('foo'=>1,'bar'=>2));

Similar to the previous examples and all examples going forward, expectation
arrays can be passed for all mock objects as the second parameter to
``mock()``.

.. code-block:: php

    $mock = \Mockery::mock('foo', function($mock) {
        $mock->shouldReceive(method_name);
    });

In addition to expectation arrays, you can also pass in a closure which
contains reusable expectations. This can be passed as the second parameter, or
as the third parameter if partnered with an expectation array. This is one
method for creating reusable mock expectations.

.. code-block:: php

    $mock = \Mockery::mock('stdClass');

Creates a mock identical to a named mock, except the name is an actual class
name. Creates a simple mock as previous examples show, except the mock object
will inherit the class type (via inheritance), i.e. it will pass type hints or
instanceof evaluations for stdClass. Useful where a mock object must be of a
specific type.

.. code-block:: php

    $mock = \Mockery::mock('FooInterface');

You can create mock objects based on any concrete class, abstract class or
even an interface. Again, the primary purpose is to ensure the mock object
inherits a specific type for type hinting. There is an exception in that
classes marked final, or with methods marked final, cannot be mocked fully. In
these cases a partial mock (explained later) must be utilised.

.. code-block:: php

    $mock = \Mockery::mock('alias:MyNamespace\MyClass');

Prefixing the valid name of a class (which is NOT currently loaded) with
"alias:" will generate an "alias mock". Alias mocks create a class alias with
the given classname to stdClass and are generally used to enable the mocking
of public static methods. Expectations set on the new mock object which refer
to static methods will be used by all static calls to this class.

.. code-block:: php

    $mock = \Mockery::mock('overload:MyNamespace\MyClass');

Prefixing the valid name of a class (which is NOT currently loaded) with
"overload:" will generate an alias mock (as with "alias:") except that created
new instances of that class will import any expectations set on the origin
mock (``$mock``). The origin mock is never verified since it's used an
expectation store for new instances. For this purpose we use the term
"instance mock" to differentiate it from the simpler "alias mock".

.. note::

    Using alias/instance mocks across more than one test will generate a fatal
    error since you can't have two classes of the same name. To avoid this,
    run each test of this kind in a separate PHP process (which is supported
    out of the box by both PHPUnit and PHPT).

.. code-block:: php

    $mock = \Mockery::mock('stdClass, MyInterface1, MyInterface2');

The first argument can also accept a list of interfaces that the mock object
must implement, optionally including no more than one existing class to be
based on. The class name doesn't need to be the first member of the list but
it's a friendly convention to use for readability. All subsequent arguments
remain unchanged from previous examples.

If the given class does not exist, you must define and include it beforehand
or a ``\Mockery\Exception`` will be thrown.

.. code-block:: php

    $mock = \Mockery::mock('MyNamespace\MyClass[foo,bar]');

The syntax above tells Mockery to partially mock the ``MyNamespace\MyClass``
class, by mocking the ``foo()`` and ``bar()`` methods only. Any other method
will be not be overridden by Mockery. This traditional form of "partial mock"
can be applied to any class or abstract class (e.g. mocking abstract methods
where a concrete implementation does not exist yet). If you attempt to partial
mock a method marked final, it will actually be ignored in that instance
leaving the final method untouched. This is necessary since mocking of final
methods is, by definition in PHP, impossible.

Please refer to ":doc:`partial_mocks`" for a detailed explanation on how to
create Partial Mocks in Mockery.

.. code-block:: php

    $mock = \Mockery::mock("MyNamespace\MyClass[foo]", array($arg1, $arg2));

If Mockery encounters an indexed array as the second or third argument, it
will assume they are constructor parameters and pass them when constructing
the mock object. The syntax above will create a new partial mock, particularly
useful if method ``bar`` calls method ``foo`` internally with
``$this->foo()``.

.. code-block:: php

    $mock = \Mockery::mock(new Foo);

Passing any real object into Mockery will create a Proxied Partial Mock. This
can be useful if real partials are impossible, e.g. a final class or class
where you absolutely must override a method marked final. Since you can
already create a concrete object, so all we need to do is selectively override
a subset of existing methods (or add non-existing methods!) for our
expectations.

A little revision: All mock methods accept the class, object or alias name to
be mocked as the first parameter. The second parameter can be an expectation
array of methods and their return values, or an expectation closure (which can
be the third param if used in conjunction with an expectation array).

.. code-block:: php

    \Mockery::self()

At times, you will discover that expectations on a mock include methods which
need to return the same mock object (e.g. a common case when designing a
Domain Specific Language (DSL) such as the one Mockery itself uses!). To
facilitate this, calling ``\Mockery::self()`` will always return the last Mock
Object created by calling ``\Mockery::mock()``. For example:

.. code-block:: php

    $mock = \Mockery::mock('BazIterator')
        ->shouldReceive('next')
        ->andReturn(\Mockery::self())
        ->mock();

The above class being mocked, as the ``next()`` method suggests, is an
iterator. In many cases, you can replace all the iterated elements (since they
are the same type many times) with just the one mock object which is
programmed to act as discrete iterated elements.

.. code-block:: php

    $mock = \Mockery::namedMock('MyClassName', 'DateTime');

The ``namedMock`` method will generate a class called by the first argument,
so in this example ``MyClassName``. The rest of the arguments are treat in the
same way as the ``mock`` method, so again, this example would create a class
called ``MyClassName`` that extends ``DateTime``.

Named mocks are quite an edge case, but they can be useful when code depends
on the ``__CLASS__`` magic constant, or when you need two derivatives of an
abstract type, that are actually different classes.

.. caution::

    You can only create a named mock once, any subsequent calls to
    ``namedMock``, with different arguments are likely to cause exceptions.

Behaviour Modifiers
-------------------

When creating a mock object, you may wish to use some commonly preferred
behaviours that are not the default in Mockery.

.. code-block:: php

    \Mockery::mock('MyClass')->shouldIgnoreMissing()

The use of the ``shouldIgnoreMissing()`` behaviour modifier will label this
mock object as a Passive Mock. In such a mock object, calls to methods which
are not covered by expectations will return ``null`` instead of the usual
complaining about there being no expectation matching the call.

You can optionally prefer to return an object of type ``\Mockery\Undefined``
(i.e.  a ``null`` object) (which was the 0.7.2 behaviour) by using an
additional modifier:

.. code-block:: php

    \Mockery::mock('MyClass')->shouldIgnoreMissing()->asUndefined()

The returned object is nothing more than a placeholder so if, by some act of
fate, it's erroneously used somewhere it shouldn't it will likely not pass a
logic check.

.. code-block:: php

    \Mockery::mock('MyClass')->makePartial()

also

.. code-block:: php

    \Mockery::mock('MyClass')->shouldDeferMissing()

Known as a Passive Partial Mock (not to be confused with real partial mock
objects discussed later), this form of mock object will defer all methods not
subject to an expectation to the parent class of the mock, i.e. ``MyClass``.
Whereas the previous ``shouldIgnoreMissing()`` returned ``null``, this
behaviour simply calls the parent's matching method.
