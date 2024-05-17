<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use DateTime;
use Hamcrest\Core\IsEqual;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\CustomValueObject;
use PHP73\CustomValueObjectInterface;
use PHP73\CustomValueObjectMatcher;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class DefaultMatchersTest extends MockeryTestCase
{
    protected $mock;

    protected function mockeryTestSetUp(): void
    {
        $this->mock = mock('foo');
    }

    public function mockeryTestTearDown(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testDefaultMatcherClass(): void
    {
        Mockery::getConfiguration()->setDefaultMatcher(CustomValueObject::class, CustomValueObjectMatcher::class);
        $this->mock->shouldReceive('foo')
            ->with(new CustomValueObject('expected'))
            ->once();
        $this->mock->foo(new CustomValueObject('expected'));
    }

    /**
     * Just a quickie roundup of a few Hamcrest matchers to check nothing obvious out of place *
     */
    public function testDefaultMatcherHamcrest(): void
    {
        Mockery::getConfiguration()->setDefaultMatcher(DateTime::class, IsEqual::class);
        $this->mock->shouldReceive('foo')
            ->with(new DateTime('2000-01-01'))
            ->once();
        $this->mock->foo(new DateTime('2000-01-01'));
    }

    public function testDefaultMatcherInterface(): void
    {
        Mockery::getConfiguration()->setDefaultMatcher(
            CustomValueObjectInterface::class,
            CustomValueObjectMatcher::class
        );
        $this->mock->shouldReceive('foo')
            ->with(new CustomValueObject('expected2'))
            ->once();
        $this->mock->foo(new CustomValueObject('expected2'));
    }
}
