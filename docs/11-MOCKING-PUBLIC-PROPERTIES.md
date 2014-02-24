# Mocking Public Properties


Mockery allows you to mock properties in several ways. The simplest is that
you can simply set a public property and value on any mock object. The second
is that you can use the expectation methods `set()` and `andSet()` to set property
values if that expectation is ever met.

You should note that, in general, Mockery does not support mocking any magic
methods since these are generally not considered a public API (and besides they
are a PITA to differentiate when you badly need them for mocking!). So please
mock virtual properties (those relying on `__get()` and `__set()`) as if they were
actually declared on the class.



**[&#8592; Previous](10-DEFAULT-MOCK-EXPECTATIONS.md) | [Contents](../README.md#documentation) | [Next &#8594;](12-MOCKING-PUBLIC-STATIC-METHODS.md)**
