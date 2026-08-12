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
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

use ReturnTypeWillChange;

use const PHP_VERSION_ID;

use function array_map;
use function array_merge;
use function array_unique;

class DefinedTargetClass implements TargetClassInterface
{
    /**
     * @var class-string
     */
    private $name;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @param class-string|null $alias
     */
    public function __construct(ReflectionClass $rfc, $alias = null)
    {
        $this->reflectionClass = $rfc;
        $this->name = $alias ?? $rfc->getName();
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
     * @param  class-string      $name
     * @param  class-string|null $alias
     * @return self
     */
    #[Override]
    public static function factory($name, $alias = null)
    {
        return new self(new ReflectionClass($name), $alias);
    }

    /**
     * @return list<class-string>
     */
    #[Override]
    public function getAttributes()
    {
        if (PHP_VERSION_ID < 80000) {
            return [];
        }

        return array_unique(
            array_merge(
                ['\AllowDynamicProperties'],
                array_map(
                    static function (ReflectionAttribute $attribute): string {
                        return '\\' . $attribute->getName();
                    },
                    $this->reflectionClass->getAttributes()
                )
            )
        );
    }

    /**
     * @return array<class-string,self>
     */
    #[Override]
    public function getInterfaces()
    {
        return array_map(
            static function (ReflectionClass $interface): self {
                return new self($interface);
            },
            $this->reflectionClass->getInterfaces()
        );
    }

    /**
     * @return list<Method>
     */
    #[Override]
    public function getMethods()
    {
        return array_map(
            static function (ReflectionMethod $method): Method {
                return new Method($method);
            },
            $this->reflectionClass->getMethods()
        );
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
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * @return string
     */
    #[Override]
    public function getShortName()
    {
        return $this->reflectionClass->getShortName();
    }

    /**
     * @return bool
     */
    #[Override]
    public function hasInternalAncestor()
    {
        if ($this->reflectionClass->isInternal()) {
            return true;
        }

        $child = $this->reflectionClass;
        while ($parent = $child->getParentClass()) {
            if ($parent->isInternal()) {
                return true;
            }

            $child = $parent;
        }

        return false;
    }

    /**
     * @param  class-string $interface
     * @return bool
     */
    #[Override]
    public function implementsInterface($interface)
    {
        return $this->reflectionClass->implementsInterface($interface);
    }

    /**
     * @return bool
     */
    #[Override]
    public function inNamespace()
    {
        return $this->reflectionClass->inNamespace();
    }

    /**
     * @return bool
     */
    #[Override]
    public function isAbstract()
    {
        return $this->reflectionClass->isAbstract();
    }

    /**
     * @return bool
     */
    #[Override]
    public function isFinal()
    {
        return $this->reflectionClass->isFinal();
    }

    /**
     * @return bool
     */
    #[Override]
    public function isReadOnly()
    {
        if (PHP_VERSION_ID < 80200) {
            return false;
        }

        return $this->reflectionClass->isReadOnly();
    }
}
