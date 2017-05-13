.. index::
    single: Upgrading

Upgrading
=========

Upgrading to 1.0.0
------------------

To be written.

Upgrading to 0.9
----------------

The generator was completely rewritten, so any code with a deep integration to
mockery will need evaluating.

Upgrading to 0.8
----------------

Since the release of 0.8.0 the following behaviours were altered:

1. The ``shouldIgnoreMissing()`` behaviour optionally applied to mock objects
   returned an instance of ``\Mockery\Undefined`` when methods called did not
   match a known expectation. Since 0.8.0, this behaviour was switched to
   returning ``null`` instead. You can restore the 0.7.2 behavour by using the
   following:

   .. code-block:: php

       $mock = \Mockery::mock('stdClass')->shouldIgnoreMissing()->asUndefined();
