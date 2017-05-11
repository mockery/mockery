.. index::
    single: Quick Reference

Quick Reference
===============

The purpose of this page is to give a quick and short overview of some of the
most common Mockery features.

Do read the :doc:`../reference/index` to learn about all the Mockery features.

Integrate Mockery with PHPUnit, either by extending the ``MockeryTestCase``:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class MyTest extends MockeryTestCase
    {
    }

or by using the ``MockeryPHPUnitIntegration`` trait:

.. code-block:: php

    use \PHPUnit\Framework\TestCase;
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    class MyTest extends TestCase
    {
        use MockeryPHPUnitIntegration;
    }

Creating a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');

Creating a test double that implements a certain interface:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass, MyInterface');

Expecting a method to be called on a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');
    $testDouble->shouldReceive('foo');

Expecting a method to **not** be called on a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');
    $testDouble->shouldNotReceive('foo');

Expecting a method to be called on a test double, once, with a certain argument,
and to return a value:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('foo')
        ->once()
        ->with($arg)
        ->andReturn($returnValue);

Expecting a method to be called on a test double and to return a different value
for each successive call:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('foo')
        ->andReturn(1, 2, 3);

    $mock->foo(); // int(1);
    $mock->foo(); // int(2);
    $mock->foo(); // int(3);
    $mock->foo(); // int(3);

Creating a runtime partial test double:

.. code-block:: php

    $mock = \Mockery::mock('MyClass')->makePartial();

Creating a spy:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

Expecting that a spy should have received a method call:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

    $spy->foo();

    $spy->shouldHaveReceived()->foo();
