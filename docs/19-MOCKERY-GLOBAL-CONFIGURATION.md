# Mockery Global Configuration


To allow for a degree of fine-tuning, Mockery utilises a singleton configuration
object to store a small subset of core behaviours. The three currently present
include:

* Option to allow/disallow the mocking of methods which do not actually exist
* Option to allow/disallow the existence of expectations which are never fulfilled (i.e. unused)
* Setter/Getter for added a parameter map for internal PHP class methods (Reflection
  cannot detect these automatically)

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

Note that when allowMockingNonExistentMethods is set to false it is still possible
to allow non-existent methods to be mocked by calling shouldAllowMockingMethod
on the mocked object:

```PHP
$mock->shouldAllowMockingMethod('someMagicMethodCall');
```

This is often preferable to using the global setting since it lets us select which
non-existent methods we want to whitelist whilst still protecting us from mock
objects and their classes going out-of-sync.


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



**[&#8592; Previous](18-DEALING-WITH-FINAL-CLASSES-OR-METHODS.md) | [Contents](../README.md#documentation) | [Next &#8594;](20-RESERVED-METHOD-NAMES.md)**
