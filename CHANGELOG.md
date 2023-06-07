# Change Log

## 1.6.2 (2023-06-07)

* [#1276: Add `IsEqual` Argument Matcher](https://github.com/mockery/mockery/pull/1276)
* [#1275: Add `IsSame` Argument Matcher](https://github.com/mockery/mockery/pull/1275)
* [#1274: Update composer branch alias](https://github.com/mockery/mockery/pull/1274)
* [#1271: Support PHP 8.2 `true` Literal Type](https://github.com/mockery/mockery/pull/1271)
* [#1270: Support PHP 8.0 `false` Literal Type](https://github.com/mockery/mockery/pull/1270)

## 1.6.1 (2023-06-05)

* [#1267 Drops support for PHP <7.4](https://github.com/mockery/mockery/pull/1267)
* [#1192 Updated changelog for version 1.5.1 to include changes from #1180](https://github.com/mockery/mockery/pull/1192)
* [#1196 Update example in README.md](https://github.com/mockery/mockery/pull/1196)
* [#1199 Fix function parameter default enum value](https://github.com/mockery/mockery/pull/1199)
* [#1205 Deal with null type in PHP8.2](https://github.com/mockery/mockery/pull/1205)
* [#1208 Import MockeryTestCase fully qualified class name](https://github.com/mockery/mockery/pull/1208)
* [#1210 Add support for target class attributes](https://github.com/mockery/mockery/pull/1210)
* [#1212 docs: Add missing comma](https://github.com/mockery/mockery/pull/1212)
* [#1216 Fixes code generation for intersection types](https://github.com/mockery/mockery/pull/1216)
* [#1217 Add MockeryExceptionInterface](https://github.com/mockery/mockery/pull/1217)
* [#1218 tidy: avoids require](https://github.com/mockery/mockery/pull/1218)
* [#1222 Add .editorconfig](https://github.com/mockery/mockery/pull/1222)
* [#1225 Switch to PSR-4 autoload](https://github.com/mockery/mockery/pull/1225)
* [#1226 Refactoring risky tests](https://github.com/mockery/mockery/pull/1226)
* [#1230 Add vimeo/psalm and psalm/plugin-phpunit](https://github.com/mockery/mockery/pull/1230)
* [#1232 Split PHPUnit TestSuites for PHP 8.2](https://github.com/mockery/mockery/pull/1232)
* [#1233 Bump actions/checkout to v3](https://github.com/mockery/mockery/pull/1233)
* [#1234 Bump nick-invision/retry to v2](https://github.com/mockery/mockery/pull/1234)
* [#1235 Setup Codecov for code coverage](https://github.com/mockery/mockery/pull/1235)
* [#1236 Add Psalm CI Check](https://github.com/mockery/mockery/pull/1236)
* [#1237 Unignore composer.lock file](https://github.com/mockery/mockery/pull/1237)
* [#1239 Prevent CI run duplication](https://github.com/mockery/mockery/pull/1239)
* [#1241 Add PHPUnit workflow for PHP 8.3](https://github.com/mockery/mockery/pull/1241)
* [#1244 Improve ClassAttributesPass for Dynamic Properties](https://github.com/mockery/mockery/pull/1244)
* [#1245 Deprecate hamcrest/hamcrest-php package](https://github.com/mockery/mockery/pull/1245)
* [#1246 Add BUG_REPORT.yml Issue template](https://github.com/mockery/mockery/pull/1246)
* [#1250 Deprecate PHP <=8.0](https://github.com/mockery/mockery/issues/1250)
* [#1253 Prevent array to string conversion when serialising a Subset matcher](https://github.com/mockery/mockery/issues/1253)

## 1.6.0 (2023-06-05) [DELETED]

This tag was deleted due to a mistake with the composer.json PHP version
constraint, see [#1266](https://github.com/mockery/mockery/issues/1266)

## 1.3.6 (2022-09-07)
* PHP 8.2 | Fix "Use of "parent" in callables is deprecated" notice #1169

## 1.5.1 (2022-09-07)

* [PHP 8.2] Various tests: explicitly declare properties #1170
* [PHP 8.2] Fix "Use of "parent" in callables is deprecated" notice #1169
* [PHP 8.1] Support intersection types #1164
* Handle final `__toString` methods #1162
* Only count assertions on expectations which can fail a test #1180

## 1.5.0 (2022-01-20)

* Override default call count expectations via expects() #1146
* Mock methods with static return types #1157
* Mock methods with mixed return type #1156
* Mock classes with new in initializers on PHP 8.1 #1160
* Removes redundant PHPUnitConstraint #1158

## 1.4.4 (2021-09-13)

* Fixes auto-generated return values #1144
* Adds support for tentative types #1130
* Fixes for PHP 8.1 Support (#1130 and #1140)
* Add method that allows defining a set of arguments the mock should yield #1133
* Added option to configure default matchers for objects `\Mockery::getConfiguration()->setDefaultMatcher($class, $matcherClass)` #1120

## 1.3.5 (2021-09-13)
* Fix auto-generated return values with union types #1143
* Adds support for tentative types #1130
* Fixes for PHP 8.1 Support (#1130 and #1140)
* Add method that allows defining a set of arguments the mock should yield #1133
* Added option to configure default matchers for objects `\Mockery::getConfiguration()->setDefaultMatcher($class, $matcherClass)` #1120

## 1.4.3 (2021-02-24)

* Fixes calls to fetchMock before initialisation #1113
* Allow shouldIgnoreMissing() to behave in a recursive fashion #1097
* Custom object formatters #766 (Needs Docs)
* Fix crash on a union type including null #1106

## 1.3.4 (2021-02-24)

* Fixes calls to fetchMock before initialisation #1113
* Fix crash on a union type including null #1106

## 1.4.2 (2020-08-11)

* Fix array to string conversion in ConstantsPass (#1086)
* Fixed nullable PHP 8.0 union types (#1088, #1089)
* Fixed support for PHP 8.0 parent type (#1088, #1089)
* Fixed PHP 8.0 mixed type support (#1088, #1089)
* Fixed PHP 8.0 union return types (#1088, #1089)

## 1.4.1 (2020-07-09)

* Allow quick definitions to use 'at least once' expectation
  `\Mockery::getConfiguration()->getQuickDefinitions()->shouldBeCalledAtLeastOnce(true)` (#1056)
* Added provisional support for PHP 8.0 (#1068, #1072,#1079)
* Fix mocking methods with iterable return type without specifying a return value (#1075)

## 1.3.3 (2020-08-11)

* Fix array to string conversion in ConstantsPass (#1086)
* Fixed nullable PHP 8.0 union types (#1088)
* Fixed support for PHP 8.0 parent type (#1088)
* Fixed PHP 8.0 mixed type support (#1088)
* Fixed PHP 8.0 union return types (#1088)

## 1.3.2 (2020-07-09)

* Fix mocking with anonymous classes (#1039)
* Fix andAnyOthers() to properly match earlier expectations (#1051)
* Added provisional support for PHP 8.0 (#1068, #1072,#1079)
* Fix mocking methods with iterable return type without specifying a return value (#1075)

## 1.4.0 (2020-05-19)

* Fix mocking with anonymous classes (#1039)
* Fix andAnyOthers() to properly match earlier expectations (#1051)
* Drops support for PHP < 7.3 and PHPUnit < 8 (#1059)

## 1.3.1 (2019-12-26)

* Revert improved exception debugging due to BC breaks (#1032)

## 1.3.0 (2019-11-24)

* Added capture `Mockery::capture` convenience matcher (#1020)
* Added `andReturnArg` to echo back an argument passed to a an expectation (#992)
* Improved exception debugging (#1000)
* Fixed `andSet` to not reuse properties between mock objects (#1012)

## 1.2.4 (2019-09-30)

* Fix a bug introduced with previous release, for empty method definition lists (#1009)

## 1.2.3 (2019-08-07)

* Allow mocking classes that have allows and expects methods (#868)
* Allow passing thru __call method in all mock types (experimental) (#969)
* Add support for `!` to blacklist methods (#959)
* Added `withSomeOfArgs` to partial match a list of args (#967)
* Fix chained demeter calls with type hint (#956)

## 1.2.2 (2019-02-13)

* Fix a BC breaking change for PHP 5.6/PHPUnit 5.7.27 (#947)

## 1.2.1 (2019-02-07)

* Support for PHPUnit 8 (#942)
* Allow mocking static methods called on instance (#938)

## 1.2.0 (2018-10-02)

* Starts counting default expectations towards count (#910)
* Adds workaround for some HHVM return types (#909)
* Adds PhpStorm metadata support for autocomplete etc (#904)
* Further attempts to support multiple PHPUnit versions (#903)
* Allows setting constructor expectations on instance mocks (#900)
* Adds workaround for HHVM memoization decorator (#893)
* Adds experimental support for callable spys (#712)

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
* Added possibility to add Constructor-Expections on hard dependencies, read: Mockery::mock('overload:...') (#781)

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
