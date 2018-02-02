# Upgrade

## Upgrade to 1.0.0 from 0.9.x

### Minimum PHP version

As of Mockery 1.0.0 the minimum PHP version required is 5.6.

### Using Mockery with PHPUnit

In the "old days", 0.9.x and older, the way Mockery was integrated with PHPUnit was
through a PHPUnit listener. That listener would in turn call the `\Mockery::close()`
method for us.

As of 1.0.0, PHPUnit test cases where we want to use Mockery, should either use the
`\Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration` trait, or extend the
`\Mockery\Adapter\Phpunit\MockeryTestCase` test case. This will in turn call the
`\Mockery::close()` method for us.

Read the documentation for a detailed overview of integrating [Mockery with PHPUnit](http://docs.mockery.io/en/latest/reference/phpunit_integration.html).

### `\Mockery\Matcher\MustBe` is deprecated

As of 1.0.0 the `\Mockery\Matcher\MustBe` matcher is deprecated and will be removed in
Mockery 2.0.0. We recommend instead to use the PHPUnit or Hamcrest equivalents of the
MustBe matcher.

### `allows` and `expects`

As of 1.0.0, Mockery has two new methods to set up expectations: `allows` and `expects`.
This means that these methods names are now "reserved" for Mockery, or in other words
classes you want to mock with Mockery, can't have methods called `allows` or `expects`.

Read more in the documentation about this [alternative shouldReceive syntax](http://docs.mockery.io/en/latest/reference/alternative_should_receive_syntax.html).

### No more implicit regex matching for string arguments

When setting up string arguments in method expectations, Mockery 0.9.x and older, would try
to match arguments using a regular expression in a "last attempt" scenario.

As of 1.0.0, Mockery will no longer attempt to do this regex matching, but will only try
first the identical operator `===`, and failing that, the equals operator `==`.

If you want to match an argument using regular expressions, please use the new
`\Mockery\Matcher\Pattern` matcher. Read more in the documentation about this
[pattern matcher](http://docs.mockery.io/en/latest/reference/argument_validation.html#complex-argument-validation).

### `andThrow` `\Throwable`

As of 1.0.0, the `andThrow` can now throw any `\Throwable`.
