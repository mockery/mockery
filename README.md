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

- [Installation](docs/01-INSTALLATION.md)
- [Upgrading](docs/02-UPGRADING.md)
- [Simple Example](docs/03-SIMPLE-EXAMPLE.md)
- [PHPUnit Integration](docs/04-PHPUNIT-INTEGRATION.md)
    - [Warning: PHPUnit running tests in separate processes](docs/04-PHPUNIT-INTEGRATION.md#warning-phpunit-running-tests-in-separate-processes)
- [Quick Reference](docs/05-QUICK-REFERENCE.md)
    - [Behaviour Modifiers](docs/05-QUICK-REFERENCE.md#behaviour-modifiers)
- [Expectation Declarations](docs/06-EXPECTATION DECLARATIONS.md)
- [Argument Validation](docs/07-ARGUMENT-VALIDATION.md)
- [Creating Partial Mocks](docs/08-CREATING-PARTIAL-MOCKS.md)
    - [Traditional Partial Mock](docs/08-CREATING-PARTIAL-MOCKS.md#traditional-partial-mock)
    - [Passive Partial Mock](docs/08-CREATING-PARTIAL-MOCKS.md#passive-partial-mock)
    - [Proxied Partial Mock](docs/08-CREATING-PARTIAL-MOCKS.md#proxied-partial-mock)
        - [Special Internal Cases](docs/08-CREATING-PARTIAL-MOCKS.md#special-internal-cases)
- [Detecting Mock Objects](docs/09-DETECTING-MOCK-OBJECTS.md)
- [Default Mock Expectations](docs/10-DEFAULT-MOCK-EXPECTATIONS.md)
- [Mocking Public Properties](docs/11-MOCKING-PUBLIC-PROPERTIES.md)
- [Mocking Public Static Methods](docs/12-MOCKING-PUBLIC-STATIC-METHODS.md)
- [Instance Mocking](docs/13-INSTANCE-MOCKING.md)
- [Preserving Pass-By-Reference Method Parameter Behaviour](docs/14-PRESERVING-PASS-BY-REFERENCE-PARAMETER-BEHAVIOUR.md)
- [Mocking Demeter Chains And Fluent Interfaces](docs/15-MOCKING-DEMETER-CHAINS-AND-FLUENT-INTERFACES.md)
- [Mockery Exceptions](docs/16-MOCKERY-EXCEPTIONS.md)
    - [\Mockery\Exception\InvalidCountException](docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptioninvalidcountexception)
    - [\Mockery\Exception\InvalidOrderException](docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptioninvalidorderexception)
    - [\Mockery\Exception\NoMatchingExpectationException](docs/16-MOCKERY-EXCEPTIONS.md#mockeryexceptionnomatchingexpectationexception)
- [Mock Object Recording](docs/17-MOCK-OBJECT-RECORDING.md)
- [Dealing with Final Classes/Methods](docs/18-DEALING-WITH-FINAL-CLASSES-OR-METHODS.md)
- [Mockery Global Configuration](docs/19-MOCKERY-GLOBAL-CONFIGURATION.md)
- [Reserved Method Names](docs/20-RESERVED-METHOD-NAMES.md)
- [PHP Magic Methods](docs/21-PHP-MAGIC-METHODS.md)
- [Gotchas!](docs/22-GOTCHAS.md)
- [Quick Examples](docs/23-QUICK-EXAMPLES.md)
- [Contributing](CONTRIBUTING.md)
