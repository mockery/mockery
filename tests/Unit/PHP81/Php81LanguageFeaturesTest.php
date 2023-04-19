<?php

namespace MockeryTest\Unit\PHP81;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use MockeryTest\Fixture\PHP80100\ArgumentIntersectionTypeHint;
use MockeryTest\Fixture\PHP80100\ClassThatImplementsSerializable;
use MockeryTest\Fixture\PHP80100\ClassWithNewInInitializer;
use MockeryTest\Fixture\PHP80100\IntersectionTypeHelper1Interface;
use MockeryTest\Fixture\PHP80100\IntersectionTypeHelperClass;
use MockeryTest\Fixture\PHP80100\NeverReturningTypehintClass;
use MockeryTest\Fixture\PHP80100\ReturnTypeWillChangeAttributeWrongReturnType;
use RuntimeException;
use Serializable;
use TypeError;
use function mock;
use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;
use function spy;

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
        $this->assertInstanceOf(Serializable::class, $mock);
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
        $mock = spy(\MockeryTest\Mockery\ReturnTypeWillChangeAttributeNoReturnType::class);

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

        $this->expectException(TypeError::class);
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
}
