<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Error;
use Exception;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use Mockery\CountValidator\Exception as CountValidatorException;
use Mockery\Exception\BadMethodCallException;
use Mockery\Exception\InvalidCountException;
use Mockery\Exception\NoMatchingExpectationException;
use Mockery\MockInterface;
use Mockery\Undefined;
use OutOfBoundsException;
use Override;
use PHP73\IWater;
use PHP73\Mockery_Demeterowski;
use PHP73\Mockery_Duck;
use PHP73\Mockery_Duck_Nonswimmer;
use PHP73\Mockery_Magic;
use PHP73\Mockery_UseDemeter;
use PHP73\MockeryTest_Foo;
use PHP73\MockeryTest_InterMethod1;
use PHP73\MockeryTest_SubjectCall1;
use PHP73\MyService2;
use stdClass;
use Throwable;

use function dirname;
use function fopen;
use function get_class;
use function is_array;
use function iterator_to_array;
use function ksort;
use function mock;
use function sprintf;

/**
 * @coversDefaultClass \Mockery
 */
final class ExpectationTest extends MockeryTestCase
{
    /**
     * @var MockInterface
     */
    protected $mock;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->mock = mock('Foo');
    }

    #[Override]
    public function mockeryTestTearDown(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    /**
     * @throws Throwable
     */
    public function testAnExampleWithSomeExpectationAmends(): void
    {
        $service = mock('MyService');
        $service->shouldReceive('login')
            ->with('user', 'pass')
            ->once()
            ->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')
            ->with('php')
            ->once()
            ->andReturn(false);
        $service->shouldReceive('addBookmark')
            ->with(Mockery::pattern('/^http:/'), Mockery::type('string'))->times(3)->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')
            ->with('php')
            ->once()
            ->andReturn(true);

        self::assertTrue($service->login('user', 'pass'));
        self::assertFalse($service->hasBookmarksTagged('php'));
        self::assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        self::assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        self::assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        self::assertTrue($service->hasBookmarksTagged('php'));
    }

    /**
     * @throws Throwable
     */
    public function testAnExampleWithSomeExpectationAmendsOnCallCounts(): void
    {
        $service = mock('MyService');
        $service->shouldReceive('login')
            ->with('user', 'pass')
            ->once()
            ->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')
            ->with('php')
            ->once()
            ->andReturn(false);
        $service->shouldReceive('addBookmark')
            ->with(Mockery::pattern('/^http:/'), Mockery::type('string'))->times(3)->andReturn(true);
        $service->shouldReceive('hasBookmarksTagged')
            ->with('php')
            ->twice()
            ->andReturn(true);

        self::assertTrue($service->login('user', 'pass'));
        self::assertFalse($service->hasBookmarksTagged('php'));
        self::assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        self::assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        self::assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        self::assertTrue($service->hasBookmarksTagged('php'));
        self::assertTrue($service->hasBookmarksTagged('php'));
    }

    /**
     * @throws Throwable
     */
    public function testAnExampleWithSomeExpectationAmendsOnCallCountsPHPUnitTest(): void
    {
        /** @var MockInterface $service */
        $service = mock(MyService2::class);

        $service->expects('login')
            ->once()
            ->with('user', 'pass')
            ->andReturnTrue();

        $service->expects('hasBookmarksTagged')
            ->times(3)
            ->with('php')
            ->andReturns(false, true, true);

        $service->expects('addBookmark')
            ->times(3)
            ->andReturnTrue();

        self::assertTrue($service->login('user', 'pass'));
        self::assertFalse($service->hasBookmarksTagged('php'));
        self::assertTrue($service->addBookmark('http://example.com/1', 'some_tag1'));
        self::assertTrue($service->addBookmark('http://example.com/2', 'some_tag2'));
        self::assertTrue($service->addBookmark('http://example.com/3', 'some_tag3'));
        self::assertTrue($service->hasBookmarksTagged('php'));
        self::assertTrue($service->hasBookmarksTagged('php'));
    }

    /**
     * @throws Throwable
     */
    public function testAndAnyOtherConstraintDoesNotPreventMatchingOfRegularArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1, 2, Mockery::andAnyOthers());
        $this->expectException(Exception::class);
        $this->mock->foo(10, 2, 3, 4, 5);
    }

    /**
     * @throws Throwable
     */
    public function testAndAnyOtherConstraintMatchesTheRestOfTheArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1, 2, Mockery::andAnyOthers())->twice();
        $this->mock->foo(1, 2, 3, 4, 5);
        $this->mock->foo(1, 2, 'str', 3, 4);
    }

    /**
     * @throws Throwable
     */
    public function testAndAnyOtherConstraintMultipleExpectationsButNoOthers(): void
    {
        $this->mock->shouldReceive('foo')
            ->with('a', Mockery::andAnyOthers())->andReturn('a');
        $this->mock->shouldReceive('foo')
            ->with('b', Mockery::andAnyOthers())->andReturn('b');
        self::assertSame('a', $this->mock->foo('a'));
        self::assertSame('b', $this->mock->foo('b'));
    }

    /**
     * @throws Throwable
     */
    public function testAndReturnUndefined(): void
    {
        $mock = mock();

        $mock->shouldReceive('undefined')
            ->once()
            ->andReturnUndefined();

        self::assertInstanceOf(Undefined::class, $mock->undefined());
    }

    /**
     * @throws Throwable
     */
    public function testAndThrowExceptions(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrowExceptions([new OutOfBoundsException(), new InvalidArgumentException()]);

        try {
            $this->mock->foo();

            throw new Exception('Expected OutOfBoundsException, non thrown');
        } catch (Exception $e) {
            self::assertInstanceOf('OutOfBoundsException', $e, "Wrong or no exception thrown: {$e->getMessage()}");
        }

        try {
            $this->mock->foo();

            throw new Exception('Expected InvalidArgumentException, non thrown');
        } catch (Exception $e) {
            self::assertInstanceOf('InvalidArgumentException', $e, "Wrong or no exception thrown: {$e->getMessage()}");
        }
    }

    /**
     * @throws Throwable
     */
    public function testAndThrowExceptionsCatchNonExceptionArgument(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You must pass an array of exception objects to andThrowExceptions');

        $this->mock
            ->shouldReceive('foo')
            ->andThrowExceptions(['NotAnException']);

    }

    /**
     * @throws Throwable
     */
    public function testAndThrowsIsAnAliasToAndThrow(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrows(new OutOfBoundsException());

        $this->expectException(OutOfBoundsException::class);
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testAndYield(): void
    {
        $this->mock->shouldReceive('foo')
            ->andYield(1, 2, 3);
        self::assertSame([1, 2, 3], iterator_to_array($this->mock->foo()));
    }

    /**
     * Argument Constraint Tests
     *
     * @throws Throwable
     */
    public function testAnyConstraintMatchesAnyArg(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::any())->twice();
        $this->mock->foo(1, 2);
        $this->mock->foo(1, 'str');
    }

    /**
     * @throws Throwable
     */
    public function testAnyConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::any())->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testAnyOfConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::anyOf(1, 2))->twice();
        $this->mock->foo(2);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testAnyOfConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::anyOf(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testAnyOfConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::anyOf(1, 2));
        $this->expectException(Exception::class);
        $this->mock->foo(3);
    }

    /**
     * @throws Throwable
     */
    public function testAnyOfConstraintThrowsExceptionWhenFalseIsNotAnExpectedArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::anyOf(0, 1, 2));
        $this->expectException(Exception::class);
        $this->mock->foo(false);
    }

    /**
     * @throws Throwable
     */
    public function testAnyOfConstraintThrowsExceptionWhenTrueIsNotAnExpectedArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::anyOf(1, 2));
        $this->expectException(Exception::class);
        $this->mock->foo(true);
    }

    /**
     * @throws Throwable
     */
    public function testArrayConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('array'))->once();
        $this->mock->foo([]);
    }

    /**
     * @throws Throwable
     */
    public function testArrayConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('array'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testArrayConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('array'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testArrayContentConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::subset([
                'a' => 1,
                'b' => 2,
            ]))->once();
        $this->mock->foo([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testArrayContentConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::subset([
                'a' => 1,
                'b' => 2,
            ]))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testArrayContentConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::subset([
                'a' => 1,
                'b' => 2,
            ]));
        $this->expectException(Exception::class);
        $this->mock->foo([
            'a' => 1,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testBoolConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('bool'))->once();
        $this->mock->foo(true);
    }

    /**
     * @throws Throwable
     */
    public function testBoolConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('bool'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testBoolConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('bool'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testByDefaultOnAMockDoesSquatWithoutExpectations(): void
    {
        self::assertInstanceOf(MockInterface::class, mock('f')->byDefault());
    }

    /**
     * @throws Throwable
     */
    public function testByDefaultOperatesFromMockConstruction(): void
    {
        $container = new Container(Mockery::getDefaultGenerator(), Mockery::getDefaultLoader());
        $mock = $container->mock('f', [
            'foo' => 'rfoo',
            'bar' => 'rbar',
            'baz' => 'rbaz',
        ])->byDefault();
        $mock->shouldReceive('foo')
            ->andReturn('foobar');
        self::assertSame('foobar', $mock->foo());
        self::assertSame('rbar', $mock->bar());
        self::assertSame('rbaz', $mock->baz());
    }

    /**
     * @throws Throwable
     */
    public function testByDefaultPreventedFromSettingDefaultWhenDefaultingExpectationWasReplaced(): void
    {
        $exp = $this->mock->shouldReceive('foo')
            ->andReturn(1);
        $this->mock->shouldReceive('foo')
            ->andReturn(2);
        $this->expectException(Exception::class);
        $exp->byDefault();
    }

    /**
     * @throws Throwable
     */
    public function testCallCountingOnlyAppliesToMatchedExpectations(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->twice();
        $this->mock->shouldReceive('foo')
            ->with(3);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(2);
        $this->mock->foo(3);
    }

    /**
     * @throws Throwable
     */
    public function testCallCountingThrowsExceptionFirst(): void
    {
        $number_of_calls = 0;
        $this->mock->shouldReceive('foo')
            ->times(2)
            ->with(Mockery::on(static function ($argument) use (&$number_of_calls): bool {
                $number_of_calls++;

                return $number_of_calls <= 3;
            }));

        $this->mock->foo(1);
        $this->mock->foo(1);
        $this->expectException(CountValidatorException::class);
        $this->mock->foo(1);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCallCountingThrowsExceptionOnAnyMismatch(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->twice();
        $this->mock->shouldReceive('foo')
            ->with(3);
        $this->mock->shouldReceive('bar');
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->bar();
        $this->expectException(CountValidatorException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCallableConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('callable'))->once();
        $this->mock->foo(static function (): string {
            return 'f';
        });
    }

    /**
     * @throws Throwable
     */
    public function testCallableConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('callable'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testCallableConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('callable'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtLeastOnceAtExactlyOneCall(): void
    {
        $this->mock->shouldReceive('foo')
            ->atLeast()
            ->once();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtLeastOnceAtExactlyThreeCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atLeast()
            ->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtLeastThrowsExceptionOnTooFewCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atLeast()
            ->twice();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtLeastThrowsExceptionOnTooManyCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atMost()
            ->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtMostAtExactlyThreeCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atMost()
            ->times(3);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledAtMostOnceAtExactlyOneCall(): void
    {
        $this->mock->shouldReceive('foo')
            ->atMost()
            ->once();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledOnce(): void
    {
        $this->mock->shouldReceive('foo')
            ->once();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledOnceThrowsExceptionIfCalledThreeTimes(): void
    {
        $this->mock->shouldReceive('foo')
            ->twice();
        $this->mock->foo();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledOnceThrowsExceptionIfCalledTwice(): void
    {
        $this->mock->shouldReceive('foo')
            ->once();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledOnceThrowsExceptionIfNotCalled(): void
    {
        $this->expectException(CountValidatorException::class);
        $this->mock->shouldReceive('foo')
            ->once();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledTwice(): void
    {
        $this->mock->shouldReceive('foo')
            ->twice();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledTwiceThrowsExceptionIfNotCalled(): void
    {
        $this->mock->shouldReceive('foo')
            ->twice();
        $this->expectException(CountValidatorException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCalledZeroOrMoreTimesAtThreeCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->zeroOrMoreTimes();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testCalledZeroOrMoreTimesAtZeroCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->zeroOrMoreTimes();
    }

    /**
     * @throws Throwable
     */
    public function testCanReturnSelf(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturnSelf();
        self::assertSame($this->mock, $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCaptureStoresArgumentOfTypeArgumentClosureEvaluatesToTrue(): void
    {
        $object = new stdClass();
        $temp = null;
        $this->mock->shouldReceive('foo')
            ->with(Mockery::capture($temp))->once();
        $this->mock->foo($object);

        self::assertSame($object, $temp);
    }

    /**
     * @throws Throwable
     */
    public function testCaptureStoresArgumentOfTypeScalarClosureEvaluatesToTrue(): void
    {
        $temp = null;
        $this->mock->shouldReceive('foo')
            ->with(Mockery::capture($temp))->once();
        $this->mock->foo(4);

        self::assertSame(4, $temp);
    }

    /**
     * @throws Throwable
     */
    public function testClassConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('stdClass'))->once();
        $this->mock->foo(new stdClass());
    }

    /**
     * @throws Throwable
     */
    public function testClassConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('stdClass'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testClassConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('stdClass'));
        $this->expectException(Exception::class);
        $this->mock->foo(new Exception());
    }

    /**
     * @throws Throwable
     */
    public function testComboOfLeastAndMostCallsThrowsExceptionAtTooFewCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atleast()
            ->once()
            ->atMost()
            ->twice();
        $this->expectException(CountValidatorException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testComboOfLeastAndMostCallsThrowsExceptionAtTooManyCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atleast()
            ->once()
            ->atMost()
            ->twice();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testComboOfLeastAndMostCallsWithOneCall(): void
    {
        $this->mock->shouldReceive('foo')
            ->atleast()
            ->once()
            ->atMost()
            ->twice();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testComboOfLeastAndMostCallsWithTwoCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->atleast()
            ->once()
            ->atMost()
            ->twice();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testContainsConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::contains(1, 2))->once();
        $this->mock->foo([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testContainsConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::contains(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testContainsConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::contains(1, 2));
        $this->expectException(Exception::class);
        $this->mock->foo([
            'a' => 1,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testCountWithBecauseExceptionMessage(): void
    {
        $this->expectException(InvalidCountException::class);
        $this->expectExceptionMessageMatches(
            '/Method foo\(<Any Arguments>\) from Mockery_(.*?) should be called' . PHP_EOL . ' ' .
            'exactly 1 times but called 0 times. Because We like foo/'
        );

        $this->mock->shouldReceive('foo')
            ->once()
            ->because('We like foo');
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testDefaultExpectationsAreReplacedByLaterConcreteExpectations(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn('bar')
            ->once()
            ->byDefault();
        $this->mock->shouldReceive('foo')
            ->andReturn('baz')
            ->twice();
        self::assertSame('baz', $this->mock->foo());
        self::assertSame('baz', $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testDefaultExpectationsCanBeOrdered(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->byDefault();
        $this->mock->shouldReceive('bar')
            ->ordered()
            ->byDefault();
        $this->expectException(Exception::class);
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testDefaultExpectationsCanBeOrderedAndReplaced(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->byDefault();
        $this->mock->shouldReceive('bar')
            ->ordered()
            ->byDefault();
        $this->mock->shouldReceive('bar')
            ->ordered()
            ->once();
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->once();
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testDefaultExpectationsCanBeOverridden(): void
    {
        $this->mock->shouldReceive('foo')
            ->with('test')
            ->andReturn('bar')
            ->byDefault();
        $this->mock->shouldReceive('foo')
            ->with('test')
            ->andReturn('newbar')
            ->byDefault();
        $this->mock->foo('test');
        self::assertSame('newbar', $this->mock->foo('test'));
    }

    /**
     * @throws Throwable
     */
    public function testDefaultExpectationsValidatedInCorrectOrder(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->once()
            ->andReturn('first')
            ->byDefault();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->once()
            ->andReturn('second')
            ->byDefault();
        self::assertSame('first', $this->mock->foo(1));
        self::assertSame('second', $this->mock->foo(2));
    }

    /**
     * @throws Throwable
     */
    public function testDifferentArgumentsAndOrderingsPassWithoutException(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->ordered()
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->ordered()
            ->once();
        $this->mock->foo(1);
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testDifferentArgumentsAndOrderingsThrowExceptionWhenInWrongOrder(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->ordered();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->ordered();
        $this->expectException(Exception::class);
        $this->mock->foo(2);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testDoubleConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('double'))->once();
        $this->mock->foo(2.25);
    }

    /**
     * @throws Throwable
     */
    public function testDoubleConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('double'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testDoubleConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('double'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testDucktypeConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::ducktype('quack', 'swim'))->once();
        $this->mock->foo(new Mockery_Duck());
    }

    /**
     * @throws Throwable
     */
    public function testDucktypeConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::ducktype('quack', 'swim'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testDucktypeConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::ducktype('quack', 'swim'));
        $this->expectException(Exception::class);
        $this->mock->foo(new Mockery_Duck_Nonswimmer());
    }

    /**
     * @throws Throwable
     */
    public function testEnsuresOrderingIsCrossMockWhenGloballyFlagSet(): void
    {
        $this->mock->shouldReceive('foo')
            ->globally()
            ->ordered();
        $mock2 = mock('bar');
        $mock2->shouldReceive('bar')
            ->globally()
            ->ordered();
        $this->expectException(Exception::class);
        $mock2->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testEnsuresOrderingIsNotCrossMockByDefault(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->once();
        $mock2 = mock('bar');
        $mock2->shouldReceive('bar')
            ->ordered()
            ->once();
        $mock2->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExactCountersOverrideAnyPriorSetNonExactCounters(): void
    {
        $this->mock->shouldReceive('foo')
            ->atLeast()
            ->once()
            ->once();
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testExceptionOnArgumentIndexOutOfRange(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->mock->shouldReceive('foo')
            ->andReturnArg(2);
        $this->mock->foo(0, 1); // only pass 2 arguments so index #2 won't exist
    }

    /**
     * @throws Throwable
     */
    public function testExceptionOnInvalidArgumentIndexValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mock->shouldReceive('foo')
            ->andReturnArg('invalid');
    }

    /**
     * @throws Throwable
     */
    public function testExpectationCanBeOverridden(): void
    {
        $this->mock->shouldReceive('foo')
            ->once()
            ->andReturn('green');
        $this->mock->shouldReceive('foo')
            ->andReturn('blue');
        self::assertSame('green', $this->mock->foo());
        self::assertSame('blue', $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testExpectationCastToStringFormatting(): void
    {
        $exp = $this->mock->shouldReceive('foo')
            ->with(1, 'bar', new stdClass(), [
                'Spam' => 'Ham',
                'Bar' => 'Baz',
            ]);
        self::assertSame("[foo(1, 'bar', object(stdClass), ['Spam' => 'Ham', 'Bar' => 'Baz'])]", (string) $exp);
    }

    /**
     * @throws Throwable
     */
    public function testExpectationFallsBackToDefaultExpectationWhenConcreteExpectationsAreUsedUp(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->andReturn('bar')
            ->once()
            ->byDefault();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->andReturn('baz')
            ->once();
        self::assertSame('baz', $this->mock->foo(2));
        self::assertSame('bar', $this->mock->foo(1));
    }

    /**
     * @throws Throwable
     */
    public function testExpectationMatchingWithAnyArgsOrderings(): void
    {
        $this->mock->shouldReceive('foo')
            ->withAnyArgs()
            ->once()
            ->ordered();
        $this->mock->shouldReceive('bar')
            ->withAnyArgs()
            ->once()
            ->ordered();
        $this->mock->shouldReceive('foo')
            ->withAnyArgs()
            ->once()
            ->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExpectationMatchingWithNoArgsOrderings(): void
    {
        $this->mock->shouldReceive('foo')
            ->withNoArgs()
            ->once()
            ->ordered();
        $this->mock->shouldReceive('bar')
            ->withNoArgs()
            ->once()
            ->ordered();
        $this->mock->shouldReceive('foo')
            ->withNoArgs()
            ->once()
            ->ordered();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExpectationsCanBeMarkedAsDefaults(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn('bar')
            ->byDefault();
        self::assertSame('bar', $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testExpectsAndOthers(): void
    {
        $this->mock->shouldReceive('foo')
            ->withAndOthers(1, 2)
            ->times(2);
        $this->mock->foo(1, 2);
        $this->mock->foo(1, 2, new stdClass(), 'a', 'b', 'c');
    }

    /**
     * @throws Throwable
     */
    public function testExpectsAnyArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->withAnyArgs()
            ->times(3);
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 'k', new stdClass());
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentMatchingObjectType(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(stdClass::class)->once();
        $this->mock->foo(new stdClass());
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArray(): void
    {
        $this->mock->shouldReceive('foo')
            ->withArgs([1, 2])->once();
        $this->mock->foo(1, 2);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayAcceptAClosureThatValidatesPassedArguments(): void
    {
        $closure = static function (int $odd, int $even): bool {
            return ($odd % 2 !== 0) && ($even % 2 === 0);
        };
        $this->mock->shouldReceive('foo')
            ->withArgs($closure)
            ->once();
        $this->mock->foo(1, 2);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayClosureDoesNotThrowExceptionIfOptionalArgumentsAreMissing(): void
    {
        $closure = static function (int $odd, int $even, ?int $sum = null): bool {
            $result = ($odd % 2 !== 0) && ($even % 2 === 0);
            if (null !== $sum) {
                return $result && ($odd + $even === $sum);
            }

            return $result;
        };
        $this->mock->shouldReceive('foo')
            ->withArgs($closure)
            ->once();
        $this->mock->foo(1, 4);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayClosureDoesNotThrowExceptionIfOptionalArgumentsMathTheExpectation(): void
    {
        $closure = static function (int $odd, int $even, ?int $sum = null): bool {
            $result = ($odd % 2 !== 0) && ($even % 2 === 0);
            if (null !== $sum) {
                return $result && ($odd + $even === $sum);
            }

            return $result;
        };
        $this->mock->shouldReceive('foo')
            ->withArgs($closure)
            ->once();
        $this->mock->foo(1, 4, 5);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayClosureThrowsExceptionIfOptionalArgumentsDontMatchTheExpectation(): void
    {
        $closure = static function (int $odd, int $even, ?int $sum = null): bool {
            $result = ($odd % 2 !== 0) && ($even % 2 === 0);
            if (null !== $sum) {
                return $result && ($odd + $even === $sum);
            }

            return $result;
        };
        $this->mock->shouldReceive('foo')
            ->withArgs($closure);
        $this->expectException(Exception::class);
        $this->mock->foo(1, 4, 2);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayThrowsExceptionIfNoArgumentsPassed(): void
    {
        $this->mock->shouldReceive('foo')
            ->with();
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayThrowsExceptionIfPassedEmptyArray(): void
    {
        $this->mock->shouldReceive('foo')
            ->withArgs([]);
        $this->expectException(Exception::class);
        $this->mock->foo(1, 2);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayThrowsExceptionIfPassedWrongArgumentType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/invalid argument (.+), only array and closure are allowed/');
        $this->mock->shouldReceive('foo')
            ->withArgs(5);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayThrowsExceptionIfPassedWrongArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->withArgs([1, 2]);
        $this->expectException(Exception::class);
        $this->mock->foo(3, 4);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsArgumentsArrayThrowsExceptionWhenClosureEvaluatesToFalse(): void
    {
        $closure = static function (int $odd, int $even): bool {
            return ($odd % 2 !== 0) && ($even % 2 === 0);
        };
        $this->mock->shouldReceive('foo')
            ->withArgs($closure);
        $this->expectException(Exception::class);
        $this->mock->foo(4, 2);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsNoArgumentCalledAtLeastOnceOverridingDefaultOnceCall(): void
    {
        $this->mock->expects()
            ->foo()
            ->atLeast()
            ->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExpectsNoArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->withNoArgs()
            ->once();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExpectsNoArgumentsThrowsExceptionIfAnyPassed(): void
    {
        $this->mock->shouldReceive('foo')
            ->withNoArgs();
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsSomeOfArgumentsGivenArgsDoNotMatchRealArgsAndThrowNoMatchingException(): void
    {
        $this->mock->shouldReceive('foo')
            ->withSomeOfArgs(1, 3, 5);
        $this->expectException(NoMatchingExpectationException::class);
        $this->mock->foo(1, 2, 4, 5);
    }

    /**
     * @throws Throwable
     */
    public function testExpectsSomeOfArgumentsMatchRealArguments(): void
    {
        $this->mock->shouldReceive('foo')
            ->withSomeOfArgs(1, 3, 5)
            ->times(4);
        $this->mock->foo(1, 2, 3, 4, 5);
        $this->mock->foo(1, 3, 5, 2, 4);
        $this->mock->foo(1, 'foo', 3, 'bar', 5);
        $this->mock->foo(1, 3, 5);
        $this->mock->shouldReceive('foo')
            ->withSomeOfArgs('foo')
            ->times(2);
        $this->mock->foo('foo', 'bar');
        $this->mock->foo('bar', 'foo');
    }

    /**
     * @throws Throwable
     */
    public function testExpectsStringArgumentCalledAtLeastOnceOverridingDefaultOnceCall(): void
    {
        $this->mock->expects('foo')
            ->atLeast()
            ->once();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testExpectsStringArgumentExceptionMessageDifferentiatesBetweenNullAndEmptyString(): void
    {
        $this->mock->shouldReceive('foo')
            ->withArgs(['a string']);
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/foo\(NULL\)/');
        $this->mock->foo(null);
    }

    /**
     * @throws Throwable
     */
    public function testFloatConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('float'))->once();
        $this->mock->foo(2.25);
    }

    /**
     * @throws Throwable
     */
    public function testFloatConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('float'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testFloatConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('float'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testGlobalConfigMayForbidMockingNonExistentMethodsOnAutoDeclaredClasses(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Mockery can't find 'SomeMadeUpClass' so can't mock it");
        $mock = mock('SomeMadeUpClass');
        $mock->shouldReceive('foo');
    }

    /**
     * @throws Throwable
     */
    public function testGlobalConfigMayForbidMockingNonExistentMethodsOnClasses(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(stdClass::class);
        $this->expectException(Exception::class);
        $mock->shouldReceive('foo');
    }

    /**
     * @throws Throwable
     */
    public function testGlobalConfigMayForbidMockingNonExistentMethodsOnObjects(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(new stdClass());
        $this->expectException(Exception::class);
        $mock->shouldReceive('foo');
    }

    /**
     * @throws Throwable
     */
    public function testGlobalConfigQuickDefinitionsConfigurationDefaultExpectation(): void
    {
        Mockery::getConfiguration()->getQuickDefinitions()->shouldBeCalledAtLeastOnce(false);
        mock([
            'foo' => 1,
        ]);
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Throwable
     */
    public function testGlobalConfigQuickDefinitionsConfigurationMockAtLeastOnce(): void
    {
        Mockery::getConfiguration()->getQuickDefinitions()->shouldBeCalledAtLeastOnce(true);
        mock([
            'foo' => 1,
        ]);
        $this->expectException(InvalidCountException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testGroupedOrderingThrowsExceptionWhenCallsDisordered(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered('first');
        $this->mock->shouldReceive('bar')
            ->ordered('second');
        $this->expectException(Exception::class);
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testGroupedOrderingWithLimitsAllowsMultipleReturnValues(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->once()
            ->andReturn('first');
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->twice()
            ->andReturn('second/third');
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->andReturn('infinity');
        self::assertSame('first', $this->mock->foo(2));
        self::assertSame('second/third', $this->mock->foo(2));
        self::assertSame('second/third', $this->mock->foo(2));
        self::assertSame('infinity', $this->mock->foo(2));
        self::assertSame('infinity', $this->mock->foo(2));
        self::assertSame('infinity', $this->mock->foo(2));
    }

    /**
     * @throws Throwable
     */
    public function testGroupedUngroupedOrderingDoNotOverlap(): void
    {
        $s = $this->mock->shouldReceive('start')
            ->ordered();
        $m = $this->mock->shouldReceive('mid')
            ->ordered('foobar');
        $e = $this->mock->shouldReceive('end')
            ->ordered();
        self::assertLessThan($m->getOrderNumber(), $s->getOrderNumber());
        self::assertLessThan($e->getOrderNumber(), $m->getOrderNumber());
    }

    /**
     * @throws Throwable
     */
    public function testHasKeyConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::hasKey('c'))->once();
        $this->mock->foo([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testHasKeyConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::hasKey('a'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, [
            'a' => 1,
        ], 3);
    }

    /**
     * @throws Throwable
     */
    public function testHasKeyConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::hasKey('c'));
        $this->expectException(Exception::class);
        $this->mock->foo([
            'a' => 1,
            'b' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testHasValueConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::hasValue(1))->once();
        $this->mock->foo([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testHasValueConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::hasValue(1))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, [
            'a' => 1,
        ], 3);
    }

    /**
     * @throws Throwable
     */
    public function testHasValueConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::hasValue(2));
        $this->expectException(Exception::class);
        $this->mock->foo([
            'a' => 1,
            'b' => 3,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testIfCallingMethodWithNoExpectationsHasSpecificExceptionMessage(): void
    {
        $mock = mock(Mockery_Duck::class);

        try {
            $mock->quack();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Received ' . get_class($mock)
                . '::quack(), but no expectations were specified',
                $e->getMessage()
            );
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testIfExceptionIndicatesAbsenceOfMethodAndExpectationsOnMock(): void
    {
        $mock = mock(Mockery_Duck::class);

        try {
            $mock->nonExistent();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Method ' . get_class($mock) . '::nonExistent() does not exist on this mock object',
                $e->getMessage()
            );
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testIntConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('int'))->once();
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testIntConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('int'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testIntConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('int'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testItCanThrowAThrowable(): void
    {
        $this->expectException(Error::class);
        $this->mock->shouldReceive('foo')
            ->andThrow(new Error());
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testItUsesAMatchersToStringMethodInTheExceptionOutput(): void
    {
        $mock = Mockery::mock();

        $mock->expects()
            ->foo(Mockery::hasKey('foo'));

        $this->expectException(InvalidCountException::class);
        $this->expectExceptionMessage('Method foo(<HasKey[foo]>)');

        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testLongConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('long'))->once();
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testLongConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('long'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testLongConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('long'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testLongExpectationCastToStringFormatting(): void
    {
        $exp = $this->mock->shouldReceive('foo')
            ->with([
                'Spam' => 'Ham',
                'Bar' => 'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'Bar',
                'Baz',
                'End',
            ]);
        self::assertSame(
            "[foo(['Spam' => 'Ham', 'Bar' => 'Baz', 0 => 'Bar', 1 => 'Baz', 2 => 'Bar', 3 => 'Baz', 4 => 'Bar', 5 => 'Baz', 6 => 'Bar', 7 => 'Baz', 8 => 'Bar', 9 => 'Baz', 10 => 'Bar', 11 => 'Baz', 12 => 'Bar', 13 => 'Baz', 14 => 'Bar', 15 => 'Baz', 16 => 'Bar', 17 => 'Baz', 18 => 'Bar', 19 => 'Baz', 20 => 'Bar', 21 => 'Baz', 22 => 'Bar', 23 => 'Baz', 24 => 'Bar', 25 => 'Baz', 26 => 'Bar', 27 => 'Baz', 28 => 'Bar', 29 => 'Baz', 30 => 'Bar', 31 => 'Baz', 32 => 'Bar', 33 => 'Baz', 34 => 'Bar', 35 => 'Baz', 36 => 'Bar', 37 => 'Baz', 38 => 'Bar', 39 => 'Baz', 40 => 'Bar', 41 => 'Baz', 42 => 'Bar', 43 => 'Baz', 44 => 'Bar', 45 => 'Baz', 46 => 'Baz', 47 => 'Bar', 48 => 'Baz', 49 => 'Bar', 50 => 'Baz', 51 => 'Bar', 52 => 'Baz', 53 => 'Bar', 54 => 'Baz', 55 => 'Bar', 56 => 'Baz', 57 => 'Baz', 58 => 'Bar', 59 => 'Baz', 60 => 'Bar', 61 => 'Baz', 62 => 'Bar', 63 => 'Baz', 64 => 'Bar', 65 => 'Baz', 66 => 'Bar', 67 => 'Baz', 68 => 'Baz', 69 => 'Bar', 70 => 'Baz', 71 => 'Bar', 72 => 'Baz', 73 => 'Bar', 74 => 'Baz', 7...])]",
            (string) $exp
        );
    }

    /**
     * @throws Throwable
     */
    public function testMakePartialExpectationBasedOnArgs(): void
    {
        $mock = mock(MockeryTest_SubjectCall1::class)->makePartial();

        self::assertSame('bar', $mock->foo());
        self::assertSame('bar', $mock->foo('baz'));
        self::assertSame('bar', $mock->foo('qux'));

        $mock->shouldReceive('foo')
            ->with('baz')
            ->twice()
            ->andReturn('123');
        self::assertSame('bar', $mock->foo());
        self::assertSame('123', $mock->foo('baz'));
        self::assertSame('bar', $mock->foo('qux'));

        $mock->shouldReceive('foo')
            ->withNoArgs()
            ->once()
            ->andReturn('456');
        self::assertSame('456', $mock->foo());
        self::assertSame('123', $mock->foo('baz'));
        self::assertSame('bar', $mock->foo('qux'));
    }

    /**
     * @throws Throwable
     */
    public function testMatchPrecedenceBasedOnExpectedCallsFavouringAnyMatch(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::any())->once();
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->never();
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testMatchPrecedenceBasedOnExpectedCallsFavouringExplicitMatch(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(Mockery::any())->never();
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testMockShouldNotBeAnonymousWhenImplementingSpecificInterface(): void
    {
        $waterMock = mock(IWater::class);
        self::assertFalse($waterMock->mockery_isAnonymous());
    }

    /**
     * @throws Throwable
     */
    public function testMockedMethodsCallableFromWithinOriginalClass(): void
    {
        $mock = mock(MockeryTest_InterMethod1::class . '[doThird]');
        $mock->shouldReceive('doThird')
            ->andReturn(true);
        self::assertTrue($mock->doFirst());
    }

    /**
     * @throws Throwable
     */
    public function testMockingDemeterChainsPassesMockeryExpectationToCompositeExpectation(): void
    {
        $mock = mock(Mockery_Demeterowski::class);
        $mock->shouldReceive('foo->bar->baz')
            ->andReturn('Spam!');
        $demeter = new Mockery_UseDemeter($mock);
        self::assertSame('Spam!', $demeter->doit());
    }

    /**
     * @throws Throwable
     */
    public function testMockingDemeterChainsPassesMockeryExpectationToCompositeExpectationWithArgs(): void
    {
        $mock = mock(Mockery_Demeterowski::class);
        $mock->shouldReceive('foo->bar->baz')->andReturn('Spam!');

        $demeter = new Mockery_UseDemeter($mock);
        self::assertSame('Spam!', $demeter->doitWithArgs());
    }

    /**
     * @throws Throwable
     */
    public function testMultipleExpectationCastToStringFormatting(): void
    {
        $exp = $this->mock->shouldReceive('foo', 'bar')->with(1)->andReturn(2);

        self::assertSame('[foo(1), bar(1)]', (string) $exp);
        self::assertSame(2, $this->mock->foo(1));
        self::assertSame(2, $this->mock->bar(1));
    }

    /**
     * @throws Throwable
     */
    public function testMultipleExpectationsWithReturns(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->andReturn(10);

        $this->mock->shouldReceive('bar')
            ->with(2)
            ->andReturn(20);

        self::assertSame(10, $this->mock->foo(1));
        self::assertSame(20, $this->mock->bar(2));
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::mustBe(2))->once();
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintMatchesObjectArgumentWithEqualsComparisonNotIdentical(): void
    {
        $a = new stdClass();
        $a->foo = 1;
        $b = new stdClass();
        $b->foo = 1;
        $this->mock->shouldReceive('foo')
            ->with(Mockery::mustBe($a))->once();
        $this->mock->foo($b);
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::mustBe(2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintNonMatchingCaseWithObject(): void
    {
        $a = new stdClass();
        $a->foo = 1;
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::mustBe($a))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, $a, 3);
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::mustBe(2));
        $this->expectException(Exception::class);
        $this->mock->foo('2');
    }

    /**
     * @throws Throwable
     */
    public function testMustBeConstraintThrowsExceptionWhenConstraintUnmatchedWithObject(): void
    {
        $a = new stdClass();
        $a->foo = 1;
        $b = new stdClass();
        $b->foo = 2;
        $this->mock->shouldReceive('foo')
            ->with(Mockery::mustBe($a));
        $this->expectException(Exception::class);
        $this->mock->foo($b);
    }

    /**
     * @throws Throwable
     */
    public function testNeverCalled(): void
    {
        $this->mock->shouldReceive('foo')
            ->never();
    }

    /**
     * @throws Throwable
     */
    public function testNeverCalledThrowsExceptionOnCall(): void
    {
        $this->mock->shouldReceive('foo')
            ->never();
        $this->expectException(CountValidatorException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testNonObjectEqualsExpectation(): void
    {
        $input = [
            'club_id' => 1,
            'user_id' => 1,
            'is_admin' => 1,
        ];

        $foo = Mockery::mock();

        $foo->shouldReceive('foo')
            ->with($input)
            ->andReturn('foobar');

        // only sort the input to change the order but not the values.
        ksort($input);

        self::assertSame('foobar', $foo->foo($input));
    }

    /**
     * @throws Throwable
     */
    public function testNotAnyOfConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::notAnyOf(1, 2))->once();
        $this->mock->foo(3);
    }

    /**
     * @throws Throwable
     */
    public function testNotAnyOfConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::notAnyOf(1, 2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 4, 3);
    }

    /**
     * @throws Throwable
     */
    public function testNotAnyOfConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::notAnyOf(1, 2));
        $this->expectException(Exception::class);
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testNotConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::not(1))->once();
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testNotConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::not(2))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testNotConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::not(2));
        $this->expectException(Exception::class);
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testNullConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('null'))->once();
        $this->mock->foo(null);
    }

    /**
     * @throws Throwable
     */
    public function testNullConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('null'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testNullConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('null'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testNumericConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('numeric'))->once();
        $this->mock->foo('2');
    }

    /**
     * @throws Throwable
     */
    public function testNumericConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('numeric'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testNumericConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('numeric'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testObjectConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('object'))->once();
        $this->mock->foo(new stdClass());
    }

    /**
     * @throws Throwable
     */
    public function testObjectConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('object`'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testObjectConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('object'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testOnConstraintMatchesArgumentClosureEvaluatesToTrue(): void
    {
        $function = static function (int $arg): bool {
            return $arg % 2 === 0;
        };
        $this->mock->shouldReceive('foo')
            ->with(Mockery::on($function))->once();
        $this->mock->foo(4);
    }

    /**
     * @throws Throwable
     */
    public function testOnConstraintMatchesArgumentOfTypeArrayClosureEvaluatesToTrue(): void
    {
        $function = static function ($arg): bool {
            return is_array($arg);
        };
        $this->mock->shouldReceive('foo')
            ->with(Mockery::on($function))->once();
        $this->mock->foo([4, 5]);
    }

    /**
     * @throws Throwable
     */
    public function testOnConstraintThrowsExceptionWhenConstraintUnmatchedClosureEvaluatesToFalse(): void
    {
        $function = static function (int $arg): bool {
            return $arg % 2 === 0;
        };
        $this->mock->shouldReceive('foo')
            ->with(Mockery::on($function));
        $this->expectException(Exception::class);
        $this->mock->foo(5);
    }

    /**
     * @throws Throwable
     */
    public function testOptionalMockRetrieval(): void
    {
        $m = mock('f')
            ->shouldReceive('foo')
            ->with(1)
            ->andReturn(3)
            ->mock();
        self::assertInstanceOf(MockInterface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testOrderedCallsWithOutOfOrderError(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered();
        $this->mock->shouldReceive('bar')
            ->ordered();
        $this->expectException(Exception::class);
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testOrderedCallsWithoutError(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->once();
        $this->mock->shouldReceive('bar')
            ->ordered()
            ->once();
        $this->mock->foo();
        $this->mock->bar();
    }

    /**
     * @throws Throwable
     */
    public function testOrderingOfDefaultGrouping(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered()
            ->once();
        $this->mock->shouldReceive('bar')
            ->ordered()
            ->once();
        $this->mock->foo();
        $this->mock->bar();
    }

    /**
     * @throws Throwable
     */
    public function testOrderingOfDefaultGroupingThrowsExceptionOnWrongOrder(): void
    {
        $this->mock->shouldReceive('foo')
            ->ordered();
        $this->mock->shouldReceive('bar')
            ->ordered();
        $this->expectException(Exception::class);
        $this->mock->bar();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testOrderingUsingNamedGroups(): void
    {
        $this->mock->shouldReceive('start')
            ->ordered('start')
            ->once();
        $this->mock->shouldReceive('foo')
            ->ordered('foobar')
            ->once();
        $this->mock->shouldReceive('bar')
            ->ordered('foobar')
            ->twice();
        $this->mock->shouldReceive('final')
            ->ordered()
            ->once();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
    }

    /**
     * @throws Throwable
     */
    public function testOrderingUsingNumberedGroups(): void
    {
        $this->mock->shouldReceive('start')
            ->ordered(1)
            ->once();
        $this->mock->shouldReceive('foo')
            ->ordered(2)
            ->once();
        $this->mock->shouldReceive('bar')
            ->ordered(2)
            ->twice();
        $this->mock->shouldReceive('final')
            ->ordered()
            ->once();
        $this->mock->start();
        $this->mock->bar();
        $this->mock->foo();
        $this->mock->bar();
        $this->mock->final();
    }

    /**
     * @throws Throwable
     */
    public function testPassthruCallMagic(): void
    {
        $mock = mock(Mockery_Magic::class);
        $mock->shouldReceive('theAnswer')
            ->once()
            ->passthru();
        self::assertSame(42, $mock->theAnswer());
    }

    /**
     * @throws Throwable
     */
    public function testPassthruEnsuresRealMethodCalledForReturnValues(): void
    {
        $mock = mock(MockeryTest_SubjectCall1::class);
        $mock->shouldReceive('foo')
            ->once()
            ->passthru();
        self::assertSame('bar', $mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testPatternConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::pattern('/foo.*/'))->once();
        $this->mock->foo('foobar');
    }

    /**
     * @throws Throwable
     */
    public function testPatternConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(Mockery::pattern('/foo.*/'))->never();
        $this->mock->foo('bar');
    }

    /**
     * @throws Throwable
     */
    public function testPatternConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::pattern('/foo.*/'));
        $this->expectException(Exception::class);
        $this->mock->foo('bar');
    }

    /**
     * @throws Throwable
     */
    public function testRealConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('float'))->once();
        $this->mock->foo(2.25);
    }

    /**
     * @throws Throwable
     */
    public function testRealConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('float'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testRealConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('float'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testResourceConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('resource'))->once();
        $r = fopen(dirname(__FILE__, 3) . '/Fixture/_files/file.txt', 'rb');
        $this->mock->foo($r);
    }

    /**
     * @throws Throwable
     */
    public function testResourceConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('resource'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testResourceConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('resource'));
        $this->expectException(Exception::class);
        $this->mock->foo('f');
    }

    /**
     * @throws Throwable
     */
    public function testReturnAsUndefinedAllowsForInfiniteSelfReturningChain(): void
    {
        $this->mock->shouldIgnoreMissing()
            ->asUndefined();
        self::assertInstanceOf(Undefined::class, $this->mock->g(1, 2)->a()->b()->c());
    }

    /**
     * @throws Throwable
     */
    public function testReturnNullIfIgnoreMissingMethodsSet(): void
    {
        $this->mock->shouldIgnoreMissing();
        self::assertNull($this->mock->g(1, 2));
    }

    /**
     * @throws Throwable
     */
    public function testReturnUndefinedIfIgnoreMissingMethodsSet(): void
    {
        $this->mock->shouldIgnoreMissing()
            ->asUndefined();
        self::assertInstanceOf(Undefined::class, $this->mock->g(1, 2));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsFalseIfFalseIsReturnValue(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturnFalse();
        self::assertFalse($this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullArgument(): void
    {
        $args = [1, null, 3];
        $index = 1;
        $this->mock->shouldReceive('foo')
            ->withArgs($args)
            ->andReturnArg($index);
        self::assertNull($this->mock->foo(...$args));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullForMockedExistingClassIfAndReturnNullCalled(): void
    {
        $mock = mock(MockeryTest_Foo::class);
        $mock->shouldReceive('foo')
            ->andReturn(null);
        self::assertNull($mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullForMockedExistingClassIfNullIsReturnValue(): void
    {
        $mock = mock(MockeryTest_Foo::class);
        $mock->shouldReceive('foo')
            ->andReturnNull();
        self::assertNull($mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullIfNullIsReturnValue(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(null);
        self::assertNull($this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullWhenManyArgs(): void
    {
        $this->mock->shouldReceive('foo');
        self::assertNull($this->mock->foo('foo', [], new stdClass()));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullWhenNoArgs(): void
    {
        $this->mock->shouldReceive('foo');
        self::assertNull($this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsNullWhenSingleArg(): void
    {
        $this->mock->shouldReceive('foo');
        self::assertNull($this->mock->foo(1));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsSameValueForAllIfNoArgsExpectationAndNoneGiven(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(1);
        self::assertSame(1, $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsSameValueForAllIfNoArgsExpectationAndSomeGiven(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(1);
        self::assertSame(1, $this->mock->foo('foo'));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsTrueIfTrueIsReturnValue(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturnTrue();
        self::assertTrue($this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsUndefined(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturnUndefined();
        self::assertInstanceOf(Undefined::class, $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValueFromSequenceSequentially(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        self::assertSame(2, $this->mock->foo('foo'));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValueFromSequenceSequentiallyAndRepeatedlyReturnsFinalValueOnExtraCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(1, 2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        self::assertSame(3, $this->mock->foo('foo'));
        self::assertSame(3, $this->mock->foo('foo'));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValueFromSequenceSequentiallyAndRepeatedlyReturnsFinalValueOnExtraCallsWithManyAndReturnCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturn(1)
            ->andReturn(2, 3);
        $this->mock->foo('foo');
        $this->mock->foo('foo');
        self::assertSame(3, $this->mock->foo('foo'));
        self::assertSame(3, $this->mock->foo('foo'));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValueOfArgument(): void
    {
        $args = [1, 2, 3, 4, 5];
        $index = 2;
        $this->mock->shouldReceive('foo')
            ->withArgs($args)
            ->andReturnArg($index);
        self::assertSame($args[$index], $this->mock->foo(...$args));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValueOfClosure(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(5)
            ->andReturnUsing(static function (int $v): int {
                return $v + 1;
            });
        self::assertSame(6, $this->mock->foo(5));
    }

    /**
     * @throws Throwable
     */
    public function testReturnsValuesSetAsArray(): void
    {
        $this->mock->shouldReceive('foo')
            ->andReturnValues([1, 2, 3]);
        self::assertSame(1, $this->mock->foo());
        self::assertSame(2, $this->mock->foo());
        self::assertSame(3, $this->mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testScalarConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('scalar'))->once();
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testScalarConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('scalar'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testScalarConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('scalar'));
        $this->expectException(Exception::class);
        $this->mock->foo([]);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesCorrectlyForDifferentInstancesOfSameClass(): void
    {
        $mockInstanceOne = mock(MockeryTest_Foo::class);
        $mockInstanceTwo = mock(MockeryTest_Foo::class);

        $mockInstanceOne->shouldReceive('foo')
            ->andSet('bar', 'baz');

        $mockInstanceTwo->shouldReceive('foo')
            ->andSet('bar', 'bazz');

        $mockInstanceOne->foo();
        $mockInstanceTwo->foo();

        self::assertSame('baz', $mockInstanceOne->bar);
        self::assertSame('bazz', $mockInstanceTwo->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequested(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->andSet('bar', 'baz', 'bazz', 'bazzz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazzz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequestedMoreTimesThanSetValues(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->andSet('bar', 'baz', 'bazz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequestedMoreTimesThanSetValuesUsingAlias(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->andSet('bar', 'baz', 'bazz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequestedMoreTimesThanSetValuesWithDirectSet(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->andSet('bar', 'baz', 'bazz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->bar = null;
        $this->mock->foo();
        self::assertNull($this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequestedMoreTimesThanSetValuesWithDirectSetUsingAlias(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->set('bar', 'baz', 'bazz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->bar = null;
        $this->mock->foo();
        self::assertNull($this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertiesWhenRequestedUsingAlias(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->set('bar', 'baz', 'bazz', 'bazzz');
        self::assertEmpty($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazz', $this->mock->bar);
        $this->mock->foo();
        self::assertSame('bazzz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertyWhenRequested(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->andSet('bar', 'baz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testSetsPublicPropertyWhenRequestedUsingAlias(): void
    {
        $this->mock->bar = null;
        $this->mock->shouldReceive('foo')
            ->set('bar', 'baz');
        self::assertNull($this->mock->bar);
        $this->mock->foo();
        self::assertSame('baz', $this->mock->bar);
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingAsDefinedProxiesToUndefinedAllowingToString(): void
    {
        $this->mock->shouldIgnoreMissing()
            ->asUndefined();
        self::assertIsString("{$this->mock->g()}");
        self::assertIsString("{$this->mock}");
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingAsUndefinedFluentInterface(): void
    {
        self::assertInstanceOf(MockInterface::class, $this->mock->shouldIgnoreMissing()->asUndefined());
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingDefaultReturnValue(): void
    {
        $this->mock->shouldIgnoreMissing(1);
        self::assertSame(1, $this->mock->a());
    }

    /**
     * @issue #253
     *
     * @throws Throwable
     */
    public function testShouldIgnoreMissingDefaultSelfAndReturnsSelf(): void
    {
        $this->mock->shouldIgnoreMissing(Mockery::self());
        self::assertSame($this->mock, $this->mock->a()->b());
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingExpectationBasedOnArgs(): void
    {
        $mock = mock(MyService2::class)->shouldIgnoreMissing();
        $mock->shouldReceive('hasBookmarksTagged')
            ->with('dave')
            ->once();
        $mock->hasBookmarksTagged('dave');
        $mock->hasBookmarksTagged('padraic');
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingFluentInterface(): void
    {
        self::assertInstanceOf(MockInterface::class, $this->mock->shouldIgnoreMissing());
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceive(): void
    {
        $this->mock->shouldNotReceive('foo');
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveCanBeAddedToCompositeExpectation(): void
    {
        $mock = mock('Foo');
        $mock->shouldReceive('a')
            ->once()
            ->andReturn('Spam!')
            ->shouldNotReceive('b');
        $mock->a();
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveThrowsExceptionIfMethodCalled(): void
    {
        $this->mock->shouldNotReceive('foo');
        $this->expectException(InvalidCountException::class);
        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveWithArgumentThrowsExceptionIfMethodCalled(): void
    {
        $this->mock->shouldNotReceive('foo')
            ->with(2);
        $this->expectException(InvalidCountException::class);
        $this->mock->foo(2);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveWithMultipleMethodsAllGetNeverConstraint(): void
    {
        $this->mock->shouldNotReceive('foo', 'bar', 'baz');

        $expectations = $this->mock->mockery_getExpectations();
        self::assertArrayHasKey('foo', $expectations);
        self::assertArrayHasKey('bar', $expectations);
        self::assertArrayHasKey('baz', $expectations);

        $director = $expectations['bar'];
        foreach ($director->getExpectations() as $expectation) {
            self::assertTrue($expectation->isCallCountConstrained());
        }
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveWithMultipleMethodsThrowsOnAnyCall(): void
    {
        $mock = mock(stdClass::class);
        $mock->shouldReceive('foo', 'bar')->byDefault();
        $mock->shouldNotReceive('foo', 'bar');

        $this->expectException(InvalidCountException::class);
        $this->expectExceptionMessage(sprintf(
            'Method foo(<Any Arguments>) from %s should be called%s exactly 0 times but called 1 times.',
            get_class($mock),
            "\n"
        ));

        $mock->foo();
        $mock->bar();

        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testShouldNotReceiveWithSingleMethodStillWorks(): void
    {
        $this->mock->shouldNotReceive('foo');

        $expectations = $this->mock->mockery_getExpectations();
        self::assertArrayHasKey('foo', $expectations);
        self::assertCount(1, $expectations);
    }

    /**
     * @throws Throwable
     */
    public function testStringConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('string'))->once();
        $this->mock->foo('2');
    }

    /**
     * @throws Throwable
     */
    public function testStringConstraintNonMatchingCase(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(1, Mockery::type('string'))->never();
        $this->mock->foo();
        $this->mock->foo(1);
        $this->mock->foo(1, 2, 3);
    }

    /**
     * @throws Throwable
     */
    public function testStringConstraintThrowsExceptionWhenConstraintUnmatched(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(Mockery::type('string'));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testThrowsException(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrow(new OutOfBoundsException());
        $this->expectException(OutOfBoundsException::class);
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionBasedOnArgs(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrow('OutOfBoundsException');
        $this->expectException(OutOfBoundsException::class);
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionBasedOnArgsWithMessage(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrow('OutOfBoundsException', 'foo');

        try {
            $this->mock->foo();
        } catch (OutOfBoundsException $e) {
            self::assertSame('foo', $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionOnNoArgumentMatch(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1);
        $this->expectException(Exception::class);
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionSequentially(): void
    {
        $this->mock->shouldReceive('foo')
            ->andThrow(new Exception())
            ->andThrow(new OutOfBoundsException());

        try {
            $this->mock->foo();
        } catch (Exception $e) {
        }

        $this->expectException(OutOfBoundsException::class);
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testTimesCountCallThrowsExceptionOnTooFewCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(2);
        $this->mock->foo();
        $this->expectException(CountValidatorException::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testTimesCountCallThrowsExceptionOnTooManyCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(2);
        $this->mock->foo();
        $this->mock->foo();

        $this->expectException(CountValidatorException::class);

        $this->mock->foo();
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testTimesCountCalls(): void
    {
        $this->mock->shouldReceive('foo')
            ->times(4);
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
        $this->mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testTimesExpectationForbidsFloatNumbers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mock->shouldReceive('foo')
            ->times(1.3);
    }

    /**
     * @throws Throwable
     */
    public function testToStringMagicMethodCanBeMocked(): void
    {
        $this->mock->shouldReceive('__toString')
            ->andReturn('dave');
        self::assertSame("{$this->mock}", 'dave');
    }

    /**
     * @throws Throwable
     */
    public function testUnorderedCallsIgnoredForOrdering(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(1)
            ->ordered()
            ->once();
        $this->mock->shouldReceive('foo')
            ->with(2)
            ->times(3);
        $this->mock->shouldReceive('foo')
            ->with(3)
            ->ordered()
            ->once();
        $this->mock->foo(2);
        $this->mock->foo(1);
        $this->mock->foo(2);
        $this->mock->foo(3);
        $this->mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testWetherMockWithInterfaceOnlyCanNotImplementNonExistingMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $waterMock = Mockery::mock(IWater::class);
        $this->expectException(Exception::class);
        $waterMock
            ->shouldReceive('nonExistentMethod')
            ->once()
            ->andReturnNull();
    }
}
