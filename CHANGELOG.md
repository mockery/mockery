# Change Log

## x.y.z. (unreleased)

## 1.1.0 (2018-05-08)

* Allows use of string method names in allows and expects (#794)
* Finalises allows and expects syntax in API (#799)
* Search for handlers in a case instensitive way (#801)
* Deprecate allowMockingMethodsUnnecessarily (#808)
* Fix risky tests (#769)
* Fix namespace in TestListener (#812)
* Fixed conflicting mock names (#813)
* Clean elses (#819)
* Updated protected method mocking exception message (#826)
* Map of constants to mock (#829)
* Simplify foreach with `in_array` function (#830)
* Typehinted return value on Expectation#verify. (#832)
* Fix shouldNotHaveReceived with HigherOrderMessage (#842)
* Deprecates shouldDeferMissing (#839)
* Adds support for return type hints in Demeter chains (#848)
* Adds shouldNotReceive to composite expectation (#847)
* Fix internal error when using --static-backup (#845)
* Adds `andAnyOtherArgs` as an optional argument matcher (#860)
* Fixes namespace qualifying with namespaced named mocks (#872)

## 1.0.0 (2017-09-06)

* Destructors (`__destruct`) are stubbed out where it makes sense
* Allow passing a closure argument to `withArgs()` to validate multiple arguments at once. 
* `Mockery\Adapter\Phpunit\TestListener` has been rewritten because it
  incorrectly marked some tests as risky. It will no longer verify mock
  expectations but instead check that tests do that themselves. PHPUnit 6 is
  required if you want to use this fail safe.
* Removes SPL Class Loader
* Removed object recorder feature
* Bumped minimum PHP version to 5.6
* `andThrow` will now throw anything `\Throwable`
* Adds `allows` and `expects` syntax
* Adds optional global helpers for `mock`, `namedMock` and `spy`
* Adds ability to create objects using traits
* `Mockery\Matcher\MustBe` was deprecated
* Marked `Mockery\MockInterface` as internal
* Subset matcher matches recursively
* BC BREAK - Spies return `null` by default from ignored (non-mocked) methods with nullable return type
* Removed extracting getter methods of object instances
* BC BREAK - Remove implicit regex matching when trying to match string arguments, introduce `\Mockery::pattern()` when regex matching is needed
* Fix Mockery not getting closed in cases of failing test cases
* Fix Mockery not setting properties on overloaded instance mocks
* BC BREAK - Fix Mockery not trying default expectations if there is any concrete expectation
* BC BREAK - Mockery's PHPUnit integration will mark a test as risky if it
  thinks one it's exceptions has been swallowed in PHPUnit > 5.7.6. Use `$e->dismiss()` to dismiss.

 
## 0.9.4 (XXXX-XX-XX)

* `shouldIgnoreMissing` will respect global `allowMockingNonExistentMethods`
  config
* Some support for variadic parameters
* Hamcrest is now a required dependency
* Instance mocks now respect `shouldIgnoreMissing` call on control instance
* This will be the *last version to support PHP 5.3*
* Added `Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration` trait
* Added `makePartial` to `Mockery\MockInterface` as it was missing

## 0.9.3 (2014-12-22)

* Added a basic spy implementation
* Added `Mockery\Adapter\Phpunit\MockeryTestCase` for more reliable PHPUnit
  integration

## 0.9.2 (2014-09-03)

* Some workarounds for the serialisation problems created by changes to PHP in 5.5.13, 5.4.29,
  5.6.
* Demeter chains attempt to reuse doubles as they see fit, so for foo->bar and
  foo->baz, we'll attempt to use the same foo

## 0.9.1 (2014-05-02)

* Allow specifying consecutive exceptions to be thrown with `andThrowExceptions`
* Allow specifying methods which can be mocked when using
  `Mockery\Configuration::allowMockingNonExistentMethods(false)` with
  `Mockery\MockInterface::shouldAllowMockingMethod($methodName)`
* Added andReturnSelf method: `$mock->shouldReceive("foo")->andReturnSelf()`
* `shouldIgnoreMissing` now takes an optional value that will be return instead
  of null, e.g. `$mock->shouldIgnoreMissing($mock)`

## 0.9.0 (2014-02-05)

* Allow mocking classes with final __wakeup() method
* Quick definitions are now always `byDefault`
* Allow mocking of protected methods with `shouldAllowMockingProtectedMethods`
* Support official Hamcrest package
* Generator completely rewritten
* Easily create named mocks with namedMock
