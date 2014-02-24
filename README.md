Mockery
=======

[![Build Status](https://travis-ci.org/padraic/mockery.png?branch=master)](http://travis-ci.org/padraic/mockery)
[![Latest Stable Version](https://poser.pugx.org/mockery/mockery/v/stable.png)](https://packagist.org/packages/mockery/mockery)
[![Total Downloads](https://poser.pugx.org/mockery/mockery/downloads.png)](https://packagist.org/packages/mockery/mockery)


Mockery is a simple yet flexible PHP mock object framework for use in unit testing
with PHPUnit, PHPSpec or any other testing framework. Its core goal is to offer a
test double framework with a succinct API capable of clearly defining all possible
object operations and interactions using a human readable Domain Specific Language
(DSL). Designed as a drop in alternative to PHPUnit's phpunit-mock-objects library,
Mockery is easy to integrate with PHPUnit and can operate alongside
phpunit-mock-objects without the World ending.

Mockery is released under a New BSD License.

The current released version for PEAR is 0.9.0. Composer users may instead opt to use
the current master branch in lieu of using the more static 0.9.0 git tag.


## Mock Objects

In unit tests, mock objects simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do not
yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation up front.

The benefits of a mock object framework are to allow for the flexible generation
of such mock objects (and stubs). They allow the setting of expected method calls
and return values using a flexible API which is capable of capturing every
possible real object behaviour in way that is stated as close as possible to a
natural language description.


## Prerequisites

Mockery requires PHP 5.3.2 or greater. In addition, it is recommended to install
the Hamcrest library (see below for instructions) which contains additional
matchers used when defining expected method arguments.


## Documentation

- [Installation](https://github.com/padraic/mockery/blob/master/docs/01-INSTALLATION.md)
- [Upgrading](https://github.com/padraic/mockery/blob/master/docs/02-UPGRADING.md)
- [Simple Example](https://github.com/padraic/mockery/blob/master/docs/03-SIMPLE-EXAMPLE.md)
- [PHPUnit Integration](https://github.com/padraic/mockery/blob/master/docs/04-PHPUNIT-INTEGRATION.md)
    - [Warning: PHPUnit running tests in separate processes](https://github.com/padraic/mockery/blob/master/docs/04-PHPUNIT-INTEGRATION.md#warning-phpunit-running-tests-in-separate-processes)
- [Quick Reference](https://github.com/padraic/mockery/blob/master/docs/05-QUICK-REFERENCE.md)
    - [Behaviour Modifiers](https://github.com/padraic/mockery/blob/master/docs/05-QUICK-REFERENCE.md#behaviour-modifiers)
- [Expectation Declarations](https://github.com/padraic/mockery/blob/master/docs/06-EXPECTATION DECLARATIONS.md)
- [Argument Validation](https://github.com/padraic/mockery/blob/master/docs/07-ARGUMENT-VALIDATION.md)
- [Creating Partial Mocks](https://github.com/padraic/mockery/blob/master/docs/08-CREATING-PARTIAL-MOCKS.md)
    - [Traditional Partial Mock](https://github.com/padraic/mockery/blob/master/docs/08-CREATING-PARTIAL-MOCKS.md#traditional-partial-mock)
    - [Passive Partial Mock](https://github.com/padraic/mockery/blob/master/docs/08-CREATING-PARTIAL-MOCKS.md#passive-partial-mock)
    - [Proxied Partial Mock](https://github.com/padraic/mockery/blob/master/docs/08-CREATING-PARTIAL-MOCKS.md#proxied-partial-mock)
        - [Special Internal Cases](https://github.com/padraic/mockery/blob/master/docs/08-CREATING-PARTIAL-MOCKS.md#special-internal-cases)
- [Detecting Mock Objects](https://github.com/padraic/mockery/blob/master/docs/09-DETECTING-MOCK-OBJECTS.md)
- [Default Mock Expectations](https://github.com/padraic/mockery/blob/master/docs/10-DEFAULT-MOCK-EXPECTATIONS.md)
- [Mocking Public Properties](https://github.com/padraic/mockery/blob/master/docs/11-MOCKING-PUBLIC-PROPERTIES.md)
- [Mocking Public Static Methods](https://github.com/padraic/mockery/blob/master/docs/12-MOCKING-PUBLIC-STATIC-METHODS.md)
- [Instance Mocking](https://github.com/padraic/mockery/blob/master/docs/13-INSTANCE-MOCKING.md)
- [Preserving Pass-By-Reference Method Parameter Behaviour](https://github.com/padraic/mockery/blob/master/docs/14-PRESERVING-PASS-BY-REFERENCE-PARAMETER-BEHAVIOUR.md)
- [Mocking Demeter Chains And Fluent Interfaces](https://github.com/padraic/mockery/blob/master/docs/15-MOCKING-DEMETER-CHAINS-AND-FLUENT-INTERFACES.md)
- [Mockery Exceptions](https://github.com/padraic/mockery/blob/master/docs/16-MOCKERY-EXCEPTIONS.md)
    - [\Mockery\Exception\InvalidCountException](https://github.com/padraic/mockery/blob/master/docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptioninvalidcountexception)
    - [\Mockery\Exception\InvalidOrderException](https://github.com/padraic/mockery/blob/master/docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptioninvalidorderexception)
    - [\Mockery\Exception\NoMatchingExpectationException](https://github.com/padraic/mockery/blob/master/docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptionnomatchingexpectationexception)
- [Mock Object Recording](https://github.com/padraic/mockery/blob/master/docs/17-MOCK-OBJECT-RECORDING.md)
- [Dealing with Final Classes/Methods](https://github.com/padraic/mockery/blob/master/docs/18-DEALING-WITH-FINAL-CLASSES-OR-METHODS.md)
- [Mockery Global Configuration](https://github.com/padraic/mockery/blob/master/docs/19-MOCKERY-GLOBAL-CONFIGURATION.md)
- [Reserved Method Names](https://github.com/padraic/mockery/blob/master/docs/20-RESERVED-METHOD-NAMES.md)
- [PHP Magic Methods](https://github.com/padraic/mockery/blob/master/docs/21-PHP-MAGIC-METHODS.md)
- [Gotchas!](https://github.com/padraic/mockery/blob/master/docs/22-GOTCHAS.md)
- [Quick Examples](https://github.com/padraic/mockery/blob/master/docs/23-QUICK-EXAMPLES.md)
- [Contributing](https://github.com/padraic/mockery/blob/master/CONTRIBUTING.md)
