.. index::
    single: Mockery; Gotchas

Gotchas!
========

Mocking objects in PHP has its limitations and gotchas. Some functionality
can't be mocked or can't be mocked YET! If you locate such a circumstance,
please please (pretty please with sugar on top) create a new issue on GitHub
so it can be documented and resolved where possible. Here is a list to note:

1. Classes containing public ``__wakeup()`` methods can be mocked but the
   mocked ``__wakeup()`` method will perform no actions and cannot have
   expectations set for it. This is necessary since Mockery must serialize and
   unserialize objects to avoid some ``__construct()`` insanity and attempting
   to mock a ``__wakeup()`` method as normal leads to a
   ``BadMethodCallException`` been thrown.

2. Classes using non-real methods, i.e. where a method call triggers a
   ``__call()`` method, will throw an exception that the non-real method does
   not exist unless you first define at least one expectation (a simple
   ``shouldReceive()`` call would suffice). This is necessary since there is
   no other way for Mockery to be aware of the method name.

3. Mockery has two scenarios where real classes are replaced: Instance mocks
   and alias mocks. Both will generate PHP fatal errors if the real class is
   loaded, usually via a require or include statement. Only use these two mock
   types where autoloading is in place and where classes are not explicitly
   loaded on a per-file basis using ``require()``, ``require_once()``, etc.

4. Internal PHP classes are not entirely capable of being fully analysed using
   ``Reflection``. For example, ``Reflection`` cannot reveal details of
   expected parameters to the methods of such internal classes. As a result,
   there will be problems where a method parameter is defined to accept a
   value by reference (Mockery cannot detect this condition and will assume a
   pass by value on scalars and arrays). If references as internal class
   method parameters are needed, you should use the
   ``\Mockery\Configuration::setInternalClassMethodParamMap()`` method.

The gotchas noted above are largely down to PHP's architecture and are assumed
to be unavoidable. But - if you figure out a solution (or a better one than
what may exist), let us know!
