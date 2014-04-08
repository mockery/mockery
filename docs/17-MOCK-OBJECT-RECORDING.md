# Mock Object Recording


In certain cases, you may find that you are testing against an already
established pattern of behaviour, perhaps during refactoring. Rather then hand
crafting mock object expectations for this behaviour, you could instead use
the existing source code to record the interactions a real object undergoes
onto a mock object as expectations - expectations you can then verify against
an alternative or refactored version of the source code.

To record expectations, you need a concrete instance of the class to be mocked.
This can then be used to create a partial mock to which is given the necessary
code to execute the object interactions to be recorded. A simple example is
outline below (we use a closure for passing instructions to the mock).

Here we have a very simple setup, a class (SubjectUser) which uses another class
(Subject) to retrieve some value. We want to record as expectations on our
mock (which will replace Subject later) all the calls and return values of
a Subject instance when interacting with SubjectUser.

```PHP
class Subject
{

    public function execute() {
        return 'executed!';
    }

}

class SubjectUser
{

    public function use(Subject $subject) {
        return $subject->execute();
    }

}
```

Here's the test case showing the recording:

```PHP
class SubjectUserTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testSomething()
    {
        $mock = \Mockery::mock(new Subject);
        $mock->shouldExpect(function ($subject) {
            $user = new SubjectUser;
            $user->use($subject);
        });

        /**
         * Assume we have a replacement SubjectUser called NewSubjectUser.
         * We want to verify it behaves identically to SubjectUser, i.e.
         * it uses Subject in the exact same way
         */
        $newSubject = new NewSubjectUser;
        $newSubject->use($mock);
    }

}
```

After the `\Mockery::close()` call in `tearDown()` validates the mock object, we
should have zero exceptions if `NewSubjectUser` acted on `Subject` in a similar way
to `SubjectUser`. By default the order of calls are not enforced, and loose argument
matching is enabled, i.e. arguments may be equal (`==`) but not necessarily identical
(`===`).

If you wished to be more strict, for example ensuring the order of calls
and the final call counts were identical, or ensuring arguments are completely
identical, you can invoke the recorder's strict mode from the closure block, e.g.

```PHP
$mock->shouldExpect(function ($subject) {
    $subject->shouldBeStrict();
    $user = new SubjectUser;
    $user->use($subject);
});
```



**[&#8592; Previous](16-MOCKERY-EXCEPTIONS.md) | [Contents](../README.md#documentation) | [Next &#8594;](18-DEALING-WITH-FINAL-CLASSES-OR-METHODS.md)**
