.. index::
    single: PHPUnit Integration

PHPUnit Integration
===================

Mockery was designed as a simple-to-use *standalone* mock object framework, so
its need for integration with any testing framework is entirely optional.  To
integrate Mockery, you just need to define a ``tearDown()`` method for your
tests containing the following (you may use a shorter ``\Mockery`` namespace
alias):

.. code-block:: php

    public function tearDown() {
        \Mockery::close();
    }

This static call cleans up the Mockery container used by the current test, and
run any verification tasks needed for your expectations.

For some added brevity when it comes to using Mockery, you can also explicitly
use the Mockery namespace with a shorter alias. For example:

.. code-block:: php

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

Mockery ships with an autoloader so you don't need to litter your tests with
``require_once()`` calls. To use it, ensure Mockery is on your
``include_path`` and add the following to your test suite's ``Bootstrap.php``
or ``TestHelper.php`` file:

.. code-block:: php

    require_once 'Mockery/Loader.php';
    require_once 'Hamcrest/Hamcrest.php';

    $loader = new \Mockery\Loader;
    $loader->register();

If you are using Composer, you can simplify this to just including the
Composer generated autoloader file:

.. code-block:: php

    require __DIR__ . '/../vendor/autoload.php'; // assuming vendor is one directory up

.. caution::

    Prior to Hamcrest 1.0.0, the ``Hamcrest.php`` file name had a small "h"
    (i.e. ``hamcrest.php``).  If upgrading Hamcrest to 1.0.0 remember to check
    the file name is updated for all your projects.)

To integrate Mockery into PHPUnit and avoid having to call the close method
and have Mockery remove itself from code coverage reports, use this in your
suite:

.. code-block:: php

    class MyTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
    {

    }

An alternative is to use the supplied trait:

.. code-block:: php

    class MyTest extends \PHPUnit_Framework_TestCase
    {
        use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    }

Mockery provides a PHPUnit listener that makes tests fail if
``Mockery::close()`` has not been called. It can help identify tests where
you've forgotten to include the trait or extend the ``MockeryTestCase``.

If you are using PHPUnit's XML configuration approach, you can include the
following to load the ``TestListener``:

.. code-block:: xml

    <listeners>
        <listener class="\Mockery\Adapter\Phpunit\TestListener"></listener>
    </listeners>

Make sure Composer's or Mockery's autoloader is present in the bootstrap file
or you will need to also define a "file" attribute pointing to the file of the
``TestListener`` class.

If you are creating the test suite programmatically you may add the listener
like this:

.. code-block:: php

    // Create the suite.
    $suite = new PHPUnit_Framework_TestSuite();

    // Create the listener and add it to the suite.
    $result = new PHPUnit_Framework_TestResult();
    $result->addListener(new \Mockery\Adapter\Phpunit\TestListener());

    // Run the tests.
    $suite->run($result);

.. caution::

    PHPUnit provides a functionality that allows
    `tests to run in a separated process <http://phpunit.de/manual/4.0/en/appendixes.annotations.html#appendixes.annotations.runTestsInSeparateProcesses>`_,
    to ensure better isolation. Mockery verifies the mocks expectations using the
    ``Mockery::close()`` method, and provides a PHPUnit listener, that automatically
    calls this method for you after every test.

    However, this listener is not called in the right process when using PHPUnit's process
    isolation, resulting in expectations that might not be respected, but without raising
    any ``Mockery\Exception``. To avoid this, you cannot rely on the supplied Mockery PHPUnit
    ``TestListener``, and you need to explicitly calls ``Mockery::close``. The easiest solution
    to include this call in the ``tearDown()`` method, as explained previously.
