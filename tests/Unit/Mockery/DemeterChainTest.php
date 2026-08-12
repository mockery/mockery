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

use mock1;
use mock2;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use Override;
use stdClass;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class DemeterChainTest extends MockeryTestCase
{
    /**
     * @var Mock
     */
    private $mock;

    /**
     * @throws Throwable
     */
    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->mock = Mockery::mock()->shouldIgnoreMissing();
    }

    #[Override]
    public function mockeryTestTearDown(): void
    {
        $this->mock->mockery_getContainer()
            ->mockery_close();
    }

    /**
     * @throws Throwable
     */
    public function testDemeterChain(): void
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('somethingElse');

        self::assertSame('somethingElse', $this->mock->getElement()->getFirst());
    }

    /**
     * @requires PHP >=8.0
     *
     * @throws Throwable
     */
    public function testDemeterChainsOnInterfaceSubjects(): void
    {
        $mock = Mockery::mock(\PHP80\PHP80TestInterface::class);

        $mock->shouldReceive('do->thing');

        self::assertInstanceOf(\PHP80\PHP80TestInterface::class, $mock->do()->thing());
    }

    /**
     * @throws Throwable
     */
    public function testDemeterChainsWithClassReturnTypeHints(): void
    {
        $a = Mockery::mock(\DemeterChain\A::class);
        $a->shouldReceive('foo->bar->baz')
            ->andReturn(new stdClass());

        $m = new \DemeterChain\Main();
        $result = $m->callDemeter($a);

        self::assertInstanceOf(stdClass::class, $result);
    }

    /**
     * @throws Throwable
     */
    public function testManyChains(): void
    {
        $this->mock->shouldReceive('getElements->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElements->getSecond')
            ->once()
            ->andReturn('somethingElse');

        $this->mock->getElements()
            ->getFirst();
        $this->mock->getElements()
            ->getSecond();
    }

    /**
     * @throws Throwable
     */
    public function testMultiLevelDemeterChain(): void
    {
        $this->mock->shouldReceive('levelOne->levelTwo->getFirst')
            ->andReturn('first');

        $this->mock->shouldReceive('levelOne->levelTwo->getSecond')
            ->andReturn('second');

        self::assertSame('second', $this->mock->levelOne()->levelTwo()->getSecond());
        self::assertSame('first', $this->mock->levelOne()->levelTwo()->getFirst());
    }

    /**
     * @throws Throwable
     */
    public function testMultipleDemeterChainsWithClassReturnTypeHints(): void
    {
        $bar = new \DemeterChain\C();
        $qux = new \DemeterChain\C();
        $a = Mockery::mock(\DemeterChain\A::class);
        $a->shouldReceive('foo->bar')
            ->andReturn($bar);
        $a->shouldReceive('foo->qux')
            ->andReturn($qux);
        self::assertSame($bar, $a->foo()->bar());
        self::assertSame($qux, $a->foo()->qux());
    }

    /**
     * @throws Throwable
     */
    public function testSimilarDemeterChainsOnDifferentClasses(): void
    {
        $mock1 = Mockery::mock('overload:mock1');
        $mock1->shouldReceive('select->some->data')
            ->andReturn(1);
        $mock1->shouldReceive('select->some->other->data')
            ->andReturn(2);

        $mock2 = Mockery::mock('overload:mock2');
        $mock2->shouldReceive('select->some->data')
            ->andReturn(3);
        $mock2->shouldReceive('select->some->other->data')
            ->andReturn(4);

        self::assertSame(1, mock1::select()->some()->data());
        self::assertSame(2, mock1::select()->some()->other()->data());
        self::assertSame(3, mock2::select()->some()->data());
        self::assertSame(4, mock2::select()->some()->other()->data());
    }

    /**
     * @throws Throwable
     */
    public function testThreeChains(): void
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        self::assertSame('something', $this->mock->getElement()->getFirst());
        self::assertSame('somethingElse', $this->mock->getElement()->getSecond());
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('somethingNew');
        self::assertSame('somethingNew', $this->mock->getElement()->getFirst());
    }

    /**
     * @throws Throwable
     */
    public function testTwoChains(): void
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        self::assertSame('something', $this->mock->getElement()->getFirst());
        self::assertSame('somethingElse', $this->mock->getElement()->getSecond());
        $this->mock->mockery_getContainer()
            ->mockery_close();
    }

    /**
     * @throws Throwable
     */
    public function testTwoChainsWithExpectedParameters(): void
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->with('parameter')
            ->andReturn('something');

        $this->mock->shouldReceive('getElement->getSecond')
            ->once()
            ->with('secondParameter')
            ->andReturn('somethingElse');

        self::assertSame('something', $this->mock->getElement()->getFirst('parameter'));
        self::assertSame('somethingElse', $this->mock->getElement()->getSecond('secondParameter'));
        $this->mock->mockery_getContainer()
            ->mockery_close();
    }

    /**
     * @throws Throwable
     */
    public function testTwoNotRelatedChains(): void
    {
        $this->mock->shouldReceive('getElement->getFirst')
            ->once()
            ->andReturn('something');

        $this->mock->shouldReceive('getOtherElement->getSecond')
            ->once()
            ->andReturn('somethingElse');

        self::assertSame('somethingElse', $this->mock->getOtherElement()->getSecond());
        self::assertSame('something', $this->mock->getElement()->getFirst());
    }
}
