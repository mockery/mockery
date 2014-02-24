# Dealing with Final Classes/Methods


One of the primary restrictions of mock objects in PHP, is that mocking classes
or methods marked final is hard. The final keyword prevents methods so marked
from being replaced in subclasses (subclassing is how mock objects can inherit
the type of the class or object being mocked.

The simplest solution is not to mark classes or methods as final!

However, in a compromise between mocking functionality and type safety, Mockery
does allow creating "proxy mocks" from classes marked final, or from classes with
methods marked final. This offers all the usual mock object goodness but the
resulting mock will not inherit the class type of the object being mocked, i.e.
it will not pass any instanceof comparison.

You can create a proxy mock by passing the instantiated object you wish to mock
into `\Mockery::mock()`, i.e. Mockery will then generate a Proxy to the real object
and selectively intercept method calls for the purposes of setting and
meeting expectations.



**[&#8592; Previous](17-MOCK-OBJECT-RECORDING.md) | [Contents](../README.md#documentation) | [Next &#8594;](19-MOCKERY-GLOBAL-CONFIGURATION.md)**
