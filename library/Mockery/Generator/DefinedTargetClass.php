<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Reflector;
use Override;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use ReturnTypeWillChange;

use const PHP_VERSION_ID;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_unique;
use function is_array;

class DefinedTargetClass implements TargetClassInterface
{
    /**
     * @var class-string
     */
    private $name;

    /**
     * @var list<PropertyHook>|null
     */
    private $propertyHooks;

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
     * @return list<PropertyHook>
     */
    #[Override]
    public function getPropertyHooks(): array
    {
        if (PHP_VERSION_ID < 80400) {
            return [];
        }

        if (is_array($this->propertyHooks)) {
            return $this->propertyHooks;
        }

        $this->propertyHooks = $properties = [];

        $reflectionClass = $this->reflectionClass;

        do {
            foreach ($reflectionClass->getProperties(
                ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
            ) as $property) {
                if ($property->isFinal()) {
                    continue;
                }

                $name = $property->getName();
                if (! array_key_exists($name, $properties)) {
                    $properties[$name] = $property;

                    continue;
                }

                $existing = $properties[$name];

                if (
                    ! $property->getDeclaringClass()->isSubclassOf($existing->getDeclaringClass()->getName())
                ) {
                    continue;
                }

                $properties[$name] = $property;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        foreach ($properties as $property) {
            /** @var array{get?:ReflectionMethod,set?:ReflectionMethod} $reflectionHooks */
            $reflectionHooks = $property->getHooks();
            if (empty($reflectionHooks)) {
                continue;
            }

            $this->propertyHooks[] = new PropertyHook(
                $property->getName(),
                $property->isPublic() ? 'public' : 'protected',
                Reflector::getPropertyTypeHint($property),
                array_key_exists('get', $reflectionHooks),
                array_key_exists('set', $reflectionHooks)
            );
        }

        return $this->propertyHooks;
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
