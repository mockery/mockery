# Upgrading to 0.9.*

0.9.0 saw the ability to mock protected properties, but this came with a slight
performance regression, requiring more reflection at runtime. We hope to resolve
this as a bug release in the 0.9 branch, but it may have to wait until 1.0.

# Upgrading to 0.8.*

Since the release of 0.8.0 the following behaviours were altered:

1. The shouldIgnoreMissing() behaviour optionally applied to mock objects returned an instance of
\Mockery\Undefined when methods called did not match a known expectation. Since 0.8.0, this behaviour
was switched to returning NULL instead. You can restore the 0.7.2 behavour by using the following:

```PHP
$mock = \Mockery::mock('stdClass')->shouldIgnoreMissing()->asUndefined();
```
