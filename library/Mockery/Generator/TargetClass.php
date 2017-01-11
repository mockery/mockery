<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Generator;

interface TargetClass
{
    /** @return string */
    public function getName();

    /** @return bool */
    public function isAbstract();

    /** @return bool */
    public function isFinal();

    /** @return Method[] */
    public function getMethods();

    /** @return string */
    public function getNamespaceName();

    /** @return bool */
    public function inNamespace();

    /** @return string */
    public function getShortName();

    /** @return bool */
    public function implementsInterface($interface);

    /** @return bool */
    public function hasInternalAncestor();
}
