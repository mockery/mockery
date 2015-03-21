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

The current released version on Packagist is 0.9.3.
The current released version for PEAR is 0.9.0. Composer users may instead opt to use
the current master branch aliased to 0.9.x-dev.

## Installation

To install Mockery, run the command below and you will get the latest
version

```sh
composer require mockery/mockery
```

If you want to run the tests:

```sh
vendor/bin/phpunit
```

####Note

The future Mockery 0.9.4 release will be the final version to have PHP 5.3
as a minimum requirement. The minimum PHP requirement will thereafter move to
PHP 5.4. Also, the PEAR channel will go offline permanently no earlier than 30
June 2015.

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

The current version can be seen at [docs.mockery.io](http://docs.mockery.io).
