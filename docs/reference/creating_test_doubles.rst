.. index::
    single: Reference; Creating Test Doubles

Creating Test Doubles
=====================

Mockery's main goal is to help us create test doubles. It can create stubs,
mocks, and spies.

Stubs and mocks are created the same. The difference between the two is that a
stub only returns a preset result when called, while a mock needs to have
expectations set on the method calls it expects to receive.

Spies are a type of test doubles that keep track of the calls they received, and
allow us to inspect these calls after the fact.

When creating a test double object, we can pass in an identifier as a name for
our test double. If we pass it no identifier, the test double name will be
unknown. Furthermore, the identifier must not be a class name. It is a
good practice, and our recommendation, to always name the test doubles with the
same name as the underlying class we are creating test doubles for.

If the identifier we use for our test double is a name of an existing class,
the test double will inherit the type of the class (via inheritance), i.e. the
mock object will pass type hints or ``instanceof`` evaluations for the existing
class. This is useful when a test double must be of a specific type, to satisfy
the expectations our code has.

Stubs and mocks
---------------

Stubs and mocks are created by calling the ``\Mockery::mock()`` method. The
following example shows how to create a stub, or a mock, object named "foo":

.. code-block:: php

    $mock = \Mockery::mock('foo');

The mock object created like this is the loosest form of mocks possible, and has
a type of ``\Mockery\Mock``.

To create a stub or a mock object with no name, we can call the ``mock()``
method with no parameters:

.. code-block:: php

    $mock = \Mockery::mock();

As we stated earlier, we don't recommend creating stub or mock objects without
a name.

Classes, abstracts, interfaces
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The recommended way is to create a stub or a mock object by using a name of
an existing class we want to create a test double of:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

This stub or mock object will have the type of ``MyClass``, through inheritance,
as well as the type of ``\Mockery\Mock``.

Stub or mock objects can be based on any concrete class, abstract class or even
an interface. The primary purpose is to ensure the mock object inherits a
specific type for type hinting.

.. code-block:: php

    $mock = \Mockery::mock('MyInterface');

This stub or mock object will implement the ``MyInterface`` interface, as well
as have the type of ``\Mockery\Mock``.

.. note::

    Classes marked final, or classes that have methods marked final cannot be
    mocked fully. Mockery supports creating partial mocks for these cases.
    Partial mocks will be explained later in the documentation.

Mockery also supports creating stub or mock objects based on a single existing
class, which must implement one or more interfaces. We can do this by providing
a comma-separated list of the class and interfaces as the first argument to the
``\Mockery::mock()`` method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass, MyInterface, OtherInterface');

This stub or mock object will now be of type ``MyClass`` and implement the
``MyInterface`` and ``OtherInterface`` interfaces.

.. note::

    The class name doesn't need to be the first member of the list but it's a
    friendly convention to use for readability.

Spies
-----

The third type of test doubles Mockery supports are spies. The main difference
between spies and mock objects is that with spies we verify the calls made
against our test double after the calls were made. We would use a spy when we
don't necessarily care about all of the calls that are going to be made to an
object.

We create a spy by calling the ``\Mockery::spy()`` method:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

Just as with stubs or mocks, we can tell Mockery to base a spy on any concrete 
or abstract class, or to implement any number of interfaces:

.. code-block:: php

    $spy = \Mockery::spy('MyClass, MyInterface, OtherInterface');

This spy will now be of type ``MyClass`` and implement the ``MyInterface`` and
``OtherInterface`` interfaces.

.. note::

    The ``\Mockery::spy()`` method call is actually a shorthand for calling
    ``\Mockery::mock()->shouldIgnoreMissing()``. The ``shouldIgnoreMissing``
    method is a "behaviour modifier". We'll discuss them a bit later.
