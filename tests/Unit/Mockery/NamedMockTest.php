<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use DateTime;
use Gardener;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Stubs\Animal;
use Stubs\Habitat;

/**
 * @coversDefaultClass \Mockery
 */
final class NamedMockTest extends MockeryTestCase
{
    public function testItCreatesANamedMock(): void
    {
        $mock = Mockery::namedMock('\Mockery\Dave123');
        self::assertInstanceOf('\Mockery\Dave123', $mock);
    }

    public function testItCreatesConcreteMethodImplementationWithReturnType(): void
    {
        $cactus = new \Nature\Plant();
        $gardener = Mockery::namedMock('\\NewNamespace\\ClassName', Gardener::class, [
            'water' => true,
        ]);
        self::assertTrue($gardener->water($cactus));
    }

    public function testItCreatesPassesFurtherArgumentsJustLikeMock(): void
    {
        $mock = Mockery::namedMock('\Mockery\Dave456', DateTime::class, [
            'getDave' => 'dave',
        ]);

        self::assertInstanceOf(DateTime::class, $mock);
        self::assertSame('dave', $mock->getDave());
    }

    public function testItGracefullyHandlesNamespacing(): void
    {
        $animal = Mockery::namedMock(\uniqid(Animal::class, false), Animal::class);

        $animal->shouldReceive('habitat')
            ->andReturn(new Habitat());

        self::assertInstanceOf(Habitat::class, $animal->habitat());
    }

    public function testItShouldThrowIfAttemptingToRedefineNamedMock(): void
    {
        $mock = Mockery::namedMock('Mockery\Dave7');
        $this->expectException(\Mockery\Exception::class);
        $this->expectExceptionMessage(
            "The mock named 'Mockery\Dave7' has been already defined with a different mock configuration"
        );
        $mock = Mockery::namedMock('Mockery\Dave7', DateTime::class);
    }
}
