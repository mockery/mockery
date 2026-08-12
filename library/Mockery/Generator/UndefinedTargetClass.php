<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Override;

use ReturnTypeWillChange;

use function array_pop;
use function explode;
use function implode;
use function ltrim;

class UndefinedTargetClass implements TargetClassInterface
{
    /**
     * @var class-string
     */
    private $name;

    /**
     * @param class-string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return class-string
     */
    #[ReturnTypeWillChange]
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param  class-string $name
     * @return self
     */
    #[Override]
    public static function factory($name)
    {
        return new self($name);
    }

    /**
     * @return list<class-string>
     */
    #[Override]
    public function getAttributes()
    {
        return [];
    }

    /**
     * @return list<self>
     */
    #[Override]
    public function getInterfaces()
    {
        return [];
    }

    /**
     * @return list<Method>
     */
    #[Override]
    public function getMethods()
    {
        return [];
    }

    /**
     * @return class-string
     */
    #[Override]
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    #[Override]
    public function getNamespaceName()
    {
        $parts = explode('\\', ltrim($this->getName(), '\\'));
        array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * @return string
     */
    #[Override]
    public function getShortName()
    {
        $parts = explode('\\', $this->getName());

        return array_pop($parts);
    }

    /**
     * @return bool
     */
    #[Override]
    public function hasInternalAncestor()
    {
        return false;
    }

    /**
     * @param  class-string $interface
     * @return bool
     */
    #[Override]
    public function implementsInterface($interface)
    {
        return false;
    }

    /**
     * @return bool
     */
    #[Override]
    public function inNamespace()
    {
        return $this->getNamespaceName() !== '';
    }

    /**
     * @return bool
     */
    #[Override]
    public function isAbstract()
    {
        return false;
    }

    /**
     * @return bool
     */
    #[Override]
    public function isFinal()
    {
        return false;
    }

    /**
     * @return bool
     */
    #[Override]
    public function isReadOnly()
    {
        return false;
    }
}
