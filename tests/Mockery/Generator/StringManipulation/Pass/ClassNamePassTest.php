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

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use PHPUnit\Framework\TestCase;

class ClassNamePassTest extends TestCase
{
    const CODE = "namespace Mockery; class Mock {}";

    public function setup()
    {
        $this->pass = new ClassNamePass();
    }

    /**
     * @test
     */
    public function shouldRemoveNamespaceDefinition()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
    }

    /**
     * @test
     */
    public function shouldReplaceNamespaceIfClassNameIsNamespaced()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
        $this->assertContains('namespace Dave;', $code);
    }

    /**
     * @test
     */
    public function shouldReplaceClassNameWithSpecifiedName()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertContains('class Dave', $code);
    }

    /**
     * @test
     */
    public function shouldRemoveLeadingBackslashesFromNamespace()
    {
        $config = new MockConfiguration(array(), array(), array(), "\Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertContains('namespace Dave;', $code);
    }
}
