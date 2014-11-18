.. index::
    single: Mocking; Partial Mocks

Creating Partial Mocks
======================

Partial mocks are useful when you only need to mock several methods of an
object leaving the remainder free to respond to calls normally (i.e.  as
implemented). Mockery implements three distinct strategies for creating
partials. Each has specific advantages and disadvantages so which strategy you
use will depend on your own preferences and the source code in need of
mocking.

#. Traditional Partial Mock
#. Passive Partial Mock
#. Proxied Partial Mock

Traditional Partial Mock
------------------------

A traditional partial mock, defines ahead of time which methods of a class are
to be mocked and which are to be left unmocked (i.e. callable as normal).  The
syntax for creating traditional mocks is:

.. code-block:: php

    $mock = \Mockery::mock('MyClass[foo,bar]');

In the above example, the ``foo()`` and ``bar()`` methods of MyClass will be
mocked but no other MyClass methods are touched. You will need to define
expectations for the ``foo()`` and ``bar()`` methods to dictate their mocked
behaviour.

Don't forget that you can pass in constructor arguments since unmocked methods
may rely on those!

.. code-block:: php

    $mock = \Mockery::mock('MyNamespace\MyClass[foo]', array($arg1, $arg2));

Passive Partial Mock
--------------------

A passive partial mock is more of a default state of being.

.. code-block:: php

    $mock = \Mockery::mock('MyClass')->makePartial();

In a passive partial, we assume that all methods will simply defer to the
parent class (``MyClass``) original methods unless a method call matches a
known expectation. If you have no matching expectation for a specific method
call, that call is deferred to the class being mocked. Since the division
between mocked and unmocked calls depends entirely on the expectations you
define, there is no need to define which methods to mock in advance. The
``makePartial()`` method is identical to the original ``shouldDeferMissing()``
method which first introduced this Partial Mock type.

Proxied Partial Mock
--------------------

A proxied partial mock is a partial of last resort. You may encounter a class
which is simply not capable of being mocked because it has been marked as
final. Similarly, you may find a class with methods marked as final. In such a
scenario, we cannot simply extend the class and override methods to mock - we
need to get creative.

.. code-block:: php

    $mock = \Mockery::mock(new MyClass);

Yes, the new mock is a Proxy. It intercepts calls and reroutes them to the
proxied object (which you construct and pass in) for methods which are not
subject to any expectations. Indirectly, this allows you to mock methods
marked final since the Proxy is not subject to those limitations. The tradeoff
should be obvious - a proxied partial will fail any typehint checks for the
class being mocked since it cannot extend that class.

Special Internal Cases
----------------------

All mock objects, with the exception of Proxied Partials, allow you to make
any expectation call the underlying real class method using the ``passthru()``
expectation call. This will return values from the real call and bypass any
mocked return queue (which can simply be omitted obviously).

There is a fourth kind of partial mock reserved for internal use. This is
automatically generated when you attempt to mock a class containing methods
marked final. Since we cannot override such methods, they are simply left
unmocked. Typically, you don't need to worry about this but if you really
really must mock a final method, the only possible means is through a Proxied
Partial Mock. SplFileInfo, for example, is a common class subject to this form
of automatic internal partial since it contains public final methods used
internally.
