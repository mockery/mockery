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

## Installation

To install Mockery, run the command below and you will get the latest
version

```sh
composer require --dev mockery/mockery
```

## Test Doubles 

Test doubles (often called mocks) simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do not
yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation up front.

The benefits of a test double framework are to allow for the flexible generation
and configuration of test doubles. They allow the setting of expected method calls
and/or return values using a flexible API which is capable of capturing every
possible real object behaviour in way that is stated as close as possible to a
natural language description. Use the `Mockery::mock` method to create a test
double.

``` php
$double = Mockery::mock();
```

If you need Mockery to create a test double to satisfy a particular type hint,
you can pass the type to the `mock` method.

``` php
class Book {}

interface BookRepository {
    function find($id): Book;
    function findAll(): array;
}

$double = Mockery::mock(BookRepository::class);
``` 

## Method Stubs

A method stub is a mechanism for having your test double return canned responses
to a certain method call. 

``` php
$double->allows()->find(123)->andReturns(new Book());
```

If your stub doesn't require specific arguments, you can also use this shortcut
for setting up multiple calls at once:

``` php
$double->allows([
    "findAll" => [new Book(), new Book()], 
]);
```

You can also use this shortcut, which creates a double and sets up some stubs in
one call:

``` php
$double = Mockery::mock(BookRepository::class, [
    "findAll" => [new Book(), new Book()], 
]);
```

## Message Expectations

## Documentation

The current version can be seen at [docs.mockery.io](http://docs.mockery.io).
