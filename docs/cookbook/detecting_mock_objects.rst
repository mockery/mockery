# Detecting Mock Objects

Users may find it useful to check whether a given object is a real object or a simulated
Mock Object. All Mockery mocks implement the `\Mockery\MockInterface` interface which can
be used in a type check.

```PHP
assert($mightBeMocked instanceof \Mockery\MockInterface);
```
