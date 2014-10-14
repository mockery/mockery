.. index::
    single: Argument Validation

Argument Validation
===================

The arguments passed to the ``with()`` declaration when setting up an
expectation determine the criteria for matching method calls to expectations.
Thus, you can setup up many expectations for a single method, each
differentiated by the expected arguments. Such argument matching is done on a
"best fit" basis.  This ensures explicit matches take precedence over
generalised matches.

An explicit match is merely where the expected argument and the actual
argument are easily equated (i.e. using ``===`` or ``==``). More generalised
matches are possible using regular expressions, class hinting and the
available generic matchers. The purpose of generalised matchers is to allow
arguments be defined in non-explicit terms, e.g. ``Mockery::any()`` passed to
``with()`` will match **any** argument in that position.

Mockery's generic matchers do not cover all possibilities but offers optional
support for the Hamcrest library of matchers. Hamcrest is a PHP port of the
similarly named Java library (which has been ported also to Python, Erlang,
etc).  I strongly recommend using Hamcrest since Mockery simply does not need
to duplicate Hamcrest's already impressive utility which itself promotes a
natural English DSL.

The example below show Mockery matchers and their Hamcrest equivalent.
Hamcrest uses functions (no namespacing).

Here's a sample of the possibilities.

.. code-block:: php

    with(1)

Matches the integer ``1``. This passes the ``===`` test (identical). It does
facilitate a less strict ``==`` check (equals) where the string ``'1'`` would
also match the
argument.

.. code-block:: php

    with(\Mockery::any()) OR with(anything())

Matches any argument. Basically, anything and everything passed in this
argument slot is passed unconstrained.

.. code-block:: php

    with(\Mockery::type('resource')) OR with(resourceValue()) OR with(typeOf('resource'))

Matches any resource, i.e. returns true from an ``is_resource()`` call. The
Type matcher accepts any string which can be attached to ``is_`` to form a
valid type check. For example, ``\Mockery::type('float')`` or Hamcrest's
``floatValue()`` and ``typeOf('float')`` checks using ``is_float()``, and
``\Mockery::type('callable')`` or Hamcrest's ``callable()`` uses
``is_callable()``.

The Type matcher also accepts a class or interface name to be used in an
``instanceof`` evaluation of the actual argument (similarly Hamcrest uses
``anInstanceOf()``).

You may find a full list of the available type checkers at
`php.net <http://www.php.net/manual/en/ref.var.php>`_ or browse Hamcrest's function
list in
`the Hamcrest code <http://code.google.com/p/hamcrest/source/browse/trunk/hamcrest-php/hamcrest/Hamcrest.php>`_.

.. code-block:: php

    with(\Mockery::on(closure))

The On matcher accepts a closure (anonymous function) to which the actual
argument will be passed. If the closure evaluates to (i.e. returns) boolean
``true`` then the argument is assumed to have matched the expectation. This is
invaluable where your argument expectation is a bit too complex for or simply
not implemented in the current default matchers.

There is no Hamcrest version of this functionality.

.. code-block:: php

    with('/^foo/') OR with(matchesPattern('/^foo/'))

The argument declarator also assumes any given string may be a regular
expression to be used against actual arguments when matching. The regex option
is only used when a) there is no ``===`` or ``==`` match and b) when the regex
is verified to be a valid regex (i.e. does not return false from
``preg_match()``).  If the regex detection doesn't suit your tastes, Hamcrest
offers the more explicit ``matchesPattern()`` function.

.. code-block:: php

    with(\Mockery::ducktype('foo', 'bar'))

The Ducktype matcher is an alternative to matching by class type. It simply
matches any argument which is an object containing the provided list of
methods to call.

There is no Hamcrest version of this functionality.

.. code-block:: php

    with(\Mockery::mustBe(2)) OR with(identicalTo(2))

The MustBe matcher is more strict than the default argument matcher. The
default matcher allows for PHP type casting, but the MustBe matcher also
verifies that the argument must be of the same type as the expected value.
Thus by default, the argument `'2'` matches the actual argument 2 (integer)
but the MustBe matcher would fail in the same situation since the expected
argument was a string and instead we got an integer.

Note: Objects are not subject to an identical comparison using this matcher
since PHP would fail the comparison if both objects were not the exact same
instance. This is a hindrance when objects are generated prior to being
returned, since an identical match just would never be possible.

.. code-block:: php

    with(\Mockery::not(2)) OR with(not(2))

The Not matcher matches any argument which is not equal or identical to the
matcher's parameter.

.. code-block:: php

    with(\Mockery::anyOf(1, 2)) OR with(anyOf(1,2))

Matches any argument which equals any one of the given parameters.

.. code-block:: php

    with(\Mockery::notAnyOf(1, 2))

Matches any argument which is not equal or identical to any of the given
parameters.

There is no Hamcrest version of this functionality.

.. code-block:: php

    with(\Mockery::subset(array(0 => 'foo')))

Matches any argument which is any array containing the given array subset.
This enforces both key naming and values, i.e. both the key and value of each
actual element is compared.

There is no Hamcrest version of this functionality, though Hamcrest can check
a single entry using ``hasEntry()`` or ``hasKeyValuePair()``.

.. code-block:: php

    with(\Mockery::contains(value1, value2))

Matches any argument which is an array containing the listed values. The
naming of keys is ignored.

.. code-block:: php

    with(\Mockery::hasKey(key));

Matches any argument which is an array containing the given key name.

.. code-block:: php

    with(\Mockery::hasValue(value));

Matches any argument which is an array containing the given value.
