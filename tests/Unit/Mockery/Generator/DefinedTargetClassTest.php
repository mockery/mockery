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

namespace MockeryTest\Unit\Mockery\Generator;

use Mockery\Generator\DefinedTargetClass;
use MockeryTest\Fixture\Generator\MockeryTest_ClassThatExtendsArrayObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DefinedTargetClassTest extends TestCase
{
    /** @test */
    public function it_knows_if_one_of_its_ancestors_is_internal()
    {
        $target = new DefinedTargetClass(new ReflectionClass(\ArrayObject::class));
        $this->assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new ReflectionClass(MockeryTest_ClassThatExtendsArrayObject::class));
        $this->assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new ReflectionClass(DefinedTargetClassTest::class));
        $this->assertFalse($target->hasInternalAncestor());
    }
}
