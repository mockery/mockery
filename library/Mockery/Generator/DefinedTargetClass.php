<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use ReflectionAttribute;
use ReflectionClass;

use function array_map;
use function array_merge;
use function array_unique;

use const PHP_VERSION_ID;

class DefinedTargetClass implements TargetClassInterface
{
    private $name;

    private $rfc;

    public function __construct(ReflectionClass $rfc, $alias = null)
    {
        $this->rfc = $rfc;
        $this->name = $alias ?? $rfc->getName();
    }

    public function __toString()
    {
        return $this->getName();
    }

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
                    $this->rfc->getAttributes()
                )
            )
        );
    }

    public function getInterfaces()
    {
        $class = self::class;
        return array_map(static function ($interface) use ($class) {
            return new $class($interface);
        }, $this->rfc->getInterfaces());
    }

    public function getMethods()
    {
        return array_map(static function ($method) {
            return new Method($method);
        }, $this->rfc->getMethods());
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespaceName()
    {
        return $this->rfc->getNamespaceName();
    }

    public function getShortName()
    {
        return $this->rfc->getShortName();
    }

    public function hasInternalAncestor()
    {
        if ($this->rfc->isInternal()) {
            return true;
        }

        $child = $this->rfc;
        while ($parent = $child->getParentClass()) {
            if ($parent->isInternal()) {
                return true;
            }

            $child = $parent;
        }

        return false;
    }

    public function implementsInterface($interface)
    {
        return $this->rfc->implementsInterface($interface);
    }

    public function inNamespace()
    {
        return $this->rfc->inNamespace();
    }

    public function isAbstract()
    {
        return $this->rfc->isAbstract();
    }

    public function isFinal()
    {
        return $this->rfc->isFinal();
    }

    public static function factory($name, $alias = null)
    {
        return new self(new ReflectionClass($name), $alias);
    }
}
