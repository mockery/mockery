<?php

namespace test\Mockery;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ReturnTypeWillChange;
use Serializable;

/**
 * @requires PHP 8.1.0-dev
 */
class Php81LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @test
     * @group issue/339
     */
    public function canMockClassesThatImplementSerializable()
    {
        $mock = mock("test\Mockery\ClassThatImplementsSerializable");
        $this->assertInstanceOf("Serializable", $mock);
    }

    /** @test */
    public function it_can_mock_an_internal_class_with_tentative_return_types()
    {
        $mock = spy(DateTime::class);

        $this->assertSame(0, $mock->getTimestamp());
    }

    /**
     * @test
     */
    public function it_can_mock_an_internal_class_with_tentative_union_return_types()
    {
        $mock = Mockery::mock('PDO');

        $this->assertInstanceOf('PDO', $mock);

        $mock->shouldReceive('exec')->once();

        try {
            $this->assertSame(0, $mock->exec('select * from foo.bar'));
        } finally {
            Mockery::close();
        }
    }

    /** @test */
    public function it_can_mock_a_class_with_return_type_will_change_attribute_and_no_return_type()
    {
        $mock = spy(ReturnTypeWillChangeAttributeNoReturnType::class);

        $this->assertNull($mock->getTimestamp());
    }

    /** @test */
    public function it_can_mock_a_class_with_return_type_will_change_attribute_and_wrong_return_type()
    {
        $mock = spy(ReturnTypeWillChangeAttributeWrongReturnType::class);

        $this->assertSame(0.0, $mock->getTimestamp());
    }

    /** @test */
    public function testMockingClassWithNewInInitializer()
    {
        $mock = Mockery::mock(ClassWithNewInInitializer::class);

        $this->assertInstanceOf(ClassWithNewInInitializer::class, $mock);
    }
}

interface LoggerInterface
{
}

class NullLogger implements LoggerInterface
{
}

class ClassWithNewInInitializer
{
    public function __construct(
        private Logger $logger = new NullLogger(),
    ) {
    }
}

class ClassThatImplementsSerializable implements Serializable
{
    public function serialize(): ?string
    {
    }

    public function __serialize(): array
    {
    }

    public function unserialize(string $data): void
    {
    }

    public function __unserialize(array $data): void
    {
    }
}

class ReturnTypeWillChangeAttributeNoReturnType extends DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp()
    {
    }
}

class ReturnTypeWillChangeAttributeWrongReturnType extends DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp(): float
    {
    }
}
