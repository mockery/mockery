# Change Log

## 0.9.11 (2019-02-12)

* Set property values on all related mocks #944

## 0.9.4 (2015-04-02)

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

* Some workarounds for the serilisation problems created by changes to PHP in 5.5.13, 5.4.29,
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
