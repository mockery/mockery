.. index::
    single: Argument Validation

Argument Validation
===================

The arguments passed to the ``with()`` declaration when setting up an
expectation determine the criteria for matching method calls to expectations.
Thus, we can setup up many expectations for a single method, each
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
etc). By using Hamcrest, Mockery does not need to duplicate Hamcrest's already
impressive utility which itself promotes a natural English DSL.

The example below show Mockery matchers and their Hamcrest equivalent.
Hamcrest uses functions (no namespacing).

Here's a sample of the possibilities.

.. code-block:: php

    with(1)

Matches the integer ``1``. This passes the ``===`` test (identical). It does
facilitate a less strict ``==`` check (equals) where the string ``'1'`` would
also match the argument.

The generic ``with()`` matching performs a strict ``===`` comparison with
objects, so only the same object ``$object`` will match:

.. code-block:: php

    $object = new stdClass();
    $mock->expects("foo")->with($object);

    // Hamcrest equivalents
    $mock->expects("foo")->with(identicalTo($object));
    $mock->expects("foo")->with(\Hamcrest\Matchers::identicalTo($object));

Another instance of ``stdClass`` will **not** match.

.. note::

    The ``Mockery\Matcher\MustBe`` matcher has been deprecated.

If we need a loose comparison of objects, we can do that using Hamcrest's
``equalTo`` matcher:

.. code-block:: php

    $mock->expects("foo")->with(equalTo(new stdClass));
    $mock->expects("foo")->with(\Hamcrest\Matchers::equalTo(new stdClass));

To match any argument, we use ``any()``:

.. code-block:: php

    with(\Mockery::any()) OR with(anything())

Anything and everything passed in this argument slot is passed unconstrained.

Validating Types and Resources
------------------------------

We can match PHP resources:

.. code-block:: php

    with(\Mockery::type('resource')) OR with(resourceValue()) OR with(typeOf('resource'))

Returns true from an ``is_resource()`` call. The ``type()`` matcher accepts any
string which can be attached to ``is_`` to form a valid type check. For example,
``\Mockery::type('float')`` or Hamcrest's ``floatValue()`` and ``typeOf('float')``
checks use ``is_float()``, and ``\Mockery::type('callable')`` or Hamcrest's
``callable()`` uses ``is_callable()``.

The ``type()`` matcher also accepts a class or interface name to be used in an
``instanceof`` evaluation of the actual argument (similarly Hamcrest uses
``anInstanceOf()``).

You may find a full list of the available type checkers at
`php.net <http://www.php.net/manual/en/ref.var.php>`_ or browse Hamcrest's function
list in
`the Hamcrest code <http://code.google.com/p/hamcrest/source/browse/trunk/hamcrest-php/hamcrest/Hamcrest.php>`_.

Complex Argument Validation
---------------------------

If we want to perform a complex argument validation, the ``on()`` matcher is
invaluable. It accepts a closure (anonymous function) to which the actual
argument will be passed.

.. code-block:: php

    with(\Mockery::on(closure))

If the closure evaluates to (i.e. returns) boolean ``true`` then the argument is
assumed to have matched the expectation.

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

    $mock->shouldReceive('foo')
        ->with(\Mockery::on(function ($argument) {
            if ($arg % 2 == 0) {
                return true;
            }
            return false;
        }));

    $mock->foo(4); // matches the expectation
    $mock->foo(3); // throws a NoMatchingExpectationException

.. note::

    There is no Hamcrest version of the ``on()`` matcher.

We can also perform argument validation by passing a closure to ``withArgs()``
method. The closure will receive all arguments passed in the call to the expected
method and if it evaluates (i.e. returns) to boolean ``true``, then the list of
arguments is assumed to have matched the expectation:

.. code-block:: php

    withArgs(closure)

The closure can also handle optional parameters, so if an optional parameter is
missing in the call to the expected method, it doesn't necessary means that the
list of arguments doesn't match the expectation.

.. code-block:: php

    $closure = function ($odd, $even, $sum = null) {
        $result = ($odd % 2 != 0) && ($even % 2 == 0);
        if (!is_null($sum)) {
            return $result && ($odd + $even == $sum);
        }
        return $result;
    };
    $this->mock->shouldReceive('foo')->withArgs($closure);

    $this->mock->foo(1, 2); // It matches the expectation: the optional argument is not needed
    $this->mock->foo(1, 2, 3); // It also matches the expectation: the optional argument pass the validation
    $this->mock->foo(1, 2, 4); // It doesn't match the expectation: the optional doesn't pass the validation

The argument matcher also assumes any given string may be a regular expression
to be used against actual arguments when matching:

.. code-block:: php

    with('/^foo/') OR with(matchesPattern('/^foo/'))

The regex option is only used when:

 a) there is no ``===`` or ``==`` match, and
 b) when the regex is verified to be a valid regex (i.e. does not return false from
    ``preg_match()``).

Hamcrest offers the more explicit ``matchesPattern()`` function.

The ``ducktype()`` matcher is an alternative to matching by class type:

.. code-block:: php

    with(\Mockery::ducktype('foo', 'bar'))

It matches any argument which is an object containing the provided list of
methods to call.

.. note::

    There is no Hamcrest version of the ``ducktype()`` matcher.

Additional Argument Matchers
----------------------------

The ``not()`` matcher matches any argument which is not equal or identical to
the matcher's parameter:

.. code-block:: php

    with(\Mockery::not(2)) OR with(not(2))

``anyOf()`` matches any argument which equals any one of the given parameters:

.. code-block:: php

    with(\Mockery::anyOf(1, 2)) OR with(anyOf(1,2))

``notAnyOf()`` matches any argument which is not equal or identical to any of
the given parameters:

.. code-block:: php

    with(\Mockery::notAnyOf(1, 2))

.. note::

    There is no Hamcrest version of the ``notAnyOf()`` matcher.

``subset()`` matches any argument which is any array containing the given array
subset:

.. code-block:: php

    with(\Mockery::subset(array(0 => 'foo')))

This enforces both key naming and values, i.e. both the key and value of each
actual element is compared.

.. note::

    There is no Hamcrest version of this functionality, though Hamcrest can check
    a single entry using ``hasEntry()`` or ``hasKeyValuePair()``.

``contains()`` matches any argument which is an array containing the listed
values:

.. code-block:: php

    with(\Mockery::contains(value1, value2))

The naming of keys is ignored.

``hasKey()`` matches any argument which is an array containing the given key
name:

.. code-block:: php

    with(\Mockery::hasKey(key));

``hasValue()`` matches any argument which is an array containing the given
value:

.. code-block:: php

    with(\Mockery::hasValue(value));
