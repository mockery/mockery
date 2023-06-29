<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

interface TargetClassInterface
{
    /**
     * Returns a new instance of the current
     * TargetClassInterface's
     * implementation.
     *
     * @param string $name
     * @return TargetClassInterface
     */
    public static function factory($name);


    /**
     * Returns the targetClass's attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Returns the targetClass's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the targetClass's methods.
     *
     * @return array
     */
    public function getMethods();

    /**
     * Returns the targetClass's interfaces.
     *
     * @return array
     */
    public function getInterfaces();

    /**
     * Returns the targetClass's namespace name.
     *
     * @return string
     */
    public function getNamespaceName();

    /**
     * Returns the targetClass's short name.
     *
     * @return string
     */
    public function getShortName();

    /**
     * Returns whether the targetClass is abstract.
     *
     * @return boolean
     */
    public function isAbstract();

    /**
     * Returns whether the targetClass is final.
     *
     * @return boolean
     */
    public function isFinal();

    /**
     * Returns whether the targetClass is in namespace.
     *
     * @return boolean
     */
    public function inNamespace();

    /**
     * Returns whether the targetClass is in
     * the passed interface.
     *
     * @param mixed $interface
     * @return boolean
     */
    public function implementsInterface($interface);

    /**
     * Returns whether the targetClass has
     * an internal ancestor.
     *
     * @return boolean
     */
    public function hasInternalAncestor();
}
