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

use DateTime;
use Gardener;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use Stubs\Animal;
use Stubs\Habitat;
use Throwable;

use function uniqid;

/**
 * @coversDefaultClass \Mockery
 */
final class NamedMockTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCreatesANamedMock(): void
    {
        $mock = Mockery::namedMock('\Mockery\Dave123');
        self::assertInstanceOf('\Mockery\Dave123', $mock);
    }

    /**
     * @throws Throwable
     */
    public function testItCreatesConcreteMethodImplementationWithReturnType(): void
    {
        $cactus = new \Nature\Plant();
        $gardener = Mockery::namedMock('\\NewNamespace\\ClassName', Gardener::class, [
            'water' => true,
        ]);
        self::assertTrue($gardener->water($cactus));
    }

    /**
     * @throws Throwable
     */
    public function testItCreatesPassesFurtherArgumentsJustLikeMock(): void
    {
        $mock = Mockery::namedMock('\Mockery\Dave456', DateTime::class, [
            'getDave' => 'dave',
        ]);

        self::assertInstanceOf(DateTime::class, $mock);
        self::assertSame('dave', $mock->getDave());
    }

    /**
     * @throws Throwable
     */
    public function testItGracefullyHandlesNamespacing(): void
    {
        $animal = Mockery::namedMock(uniqid(Animal::class, false), Animal::class);

        $animal->shouldReceive('habitat')
            ->andReturn(new Habitat());

        self::assertInstanceOf(Habitat::class, $animal->habitat());
    }

    /**
     * @throws Throwable
     */
    public function testItShouldThrowIfAttemptingToRedefineNamedMock(): void
    {
        $mock = Mockery::namedMock('Mockery\Dave7');
        self::assertInstanceOf('Mockery\Dave7', $mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "The mock named 'Mockery\Dave7' has been already defined with a different mock configuration"
        );
        Mockery::namedMock('Mockery\Dave7', DateTime::class);
    }
}
