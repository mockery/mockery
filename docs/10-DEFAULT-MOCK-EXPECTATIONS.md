# Default Mock Expectations


Often in unit testing, we end up with sets of tests which use the same object
dependency over and over again. Rather than mocking this class/object within
every single unit test (requiring a mountain of duplicate code), we can instead
define reusable default mocks within the test case's `setUp()` method. This even
works where unit tests use varying expectations on the same or similar mock
object.

How this works, is that you can define mocks with default expectations. Then,
in a later unit test, you can add or fine-tune expectations for that
specific test. Any expectation can be set as a default using the `byDefault()`
declaration.



**[&#8592; Previous](09-DETECTING-MOCK-OBJECTS.md) | [Contents](../README.md#documentation) | [Next &#8594;](11-MOCKING-PUBLIC-PROPERTIES.md)**
