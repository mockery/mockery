<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

class CustomValueObjectMatcher extends \Mockery\Matcher\AbstractMatcher
{
    public function match(mixed &$actual): bool
    {
        return $actual->value === $this->expected->value;
    }

    public function __toString()
    {
        return "<customMatcher>";
    }
}

interface CustomValueObjectInterface
{
}

class CustomValueObject implements CustomValueObjectInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

class DefaultMatchersTest extends MockeryTestCase
{
    protected $mock;

    public function mockeryTestSetUp()
    {
        parent::mockeryTestSetUp();
        $this->mock = mock('foo');
    }


    public function mockeryTestTearDown()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        parent::mockeryTestTearDown();
    }

    public function testDefaultMatcherClass()
    {
        \Mockery::getConfiguration()->setDefaultMatcher(CustomValueObject::class, CustomValueObjectMatcher::class);
        $this->mock->shouldReceive('foo')->with(new CustomValueObject("expected"))->once();
        $this->mock->foo(new CustomValueObject("expected"));
    }

    public function testDefaultMatcherInterface()
    {
        \Mockery::getConfiguration()->setDefaultMatcher(CustomValueObjectInterface::class, CustomValueObjectMatcher::class);
        $this->mock->shouldReceive('foo')->with(new CustomValueObject("expected2"))->once();
        $this->mock->foo(new CustomValueObject("expected2"));
    }
}
