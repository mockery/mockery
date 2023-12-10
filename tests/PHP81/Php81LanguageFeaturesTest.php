<?php

namespace Mockery\Tests\PHP81;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PDO;
use ReturnTypeWillChange;
use RuntimeException;
use Serializable;

use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;

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
        $mock = mock(ClassThatImplementsSerializable::class);
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
        $mock = Mockery::mock(PDO::class);

        $this->assertInstanceOf(PDO::class, $mock);

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

    /** @test */
    public function it_can_mock_a_class_with_an_intersection_argument_type_hint()
    {
        $mock = Mockery::spy(ArgumentIntersectionTypeHint::class);
        $object = new IntersectionTypeHelperClass();
        $mock->allows()->foo($object);

        $mock->foo($object);

        $this->expectException(\TypeError::class);
        $mock->foo(Mockery::mock(IntersectionTypeHelper1Interface::class));
    }

    /** @test */
    public function it_can_mock_a_class_with_a_never_returning_type_hint()
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $this->expectException(RuntimeException::class);
        $mock->throws();
    }

    /**
     * @requires extension pcntl
     * @test
     */
    public function it_can_mock_a_class_with_a_never_returning_type_hint_with_exit()
    {
        $mock = Mockery::mock(NeverReturningTypehintClass::class)->makePartial();

        $pid = pcntl_fork();

        if (-1 === $pid) {
            $this->markTestSkipped("Couldn't fork for exit test");

            return;
        } elseif ($pid) {
            pcntl_waitpid($pid, $status);
            $this->assertEquals(123, pcntl_wexitstatus($status));

            return;
        }

        $mock->exits();
    }

    /** @test */
    public function it_can_parse_enum_as_default_value_correctly()
    {
        $mock = Mockery::mock(UsesEnums::class);
        $mock->shouldReceive('set')->once();
        $mock->set();
        $this->assertEquals(SimpleEnum::first, $mock->enum); // check that mock did not set internal variable
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

class NeverReturningTypehintClass
{
    public function throws(): never
    {
        throw new RuntimeException('Never!');
    }

    public function exits(): never
    {
        exit(123);
    }
}
class IntersectionTypeHelperClass implements IntersectionTypeHelper1Interface, IntersectionTypeHelper2Interface
{
    public function foo(): int
    {
        return 123;
    }
    public function bar(): int
    {
        return 123;
    }
}
interface IntersectionTypeHelper2Interface
{
    public function foo(): int;
}
interface IntersectionTypeHelper1Interface
{
    public function bar(): int;
}

class ArgumentIntersectionTypeHint
{
    public function foo(IntersectionTypeHelper1Interface&IntersectionTypeHelper2Interface $foo)
    {
    }
}

enum SimpleEnum
{
    case first;
    case second;
}

class UsesEnums
{
    public SimpleEnum $enum = SimpleEnum::first;
    public function set(SimpleEnum $enum = SimpleEnum::second)
    {
        $this->enum = $enum;
    }
}
