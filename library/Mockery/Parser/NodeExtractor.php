<?php

declare(strict_types=1);

namespace Mockery\Parser;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\PropertyProperty;

final class NodeExtractor
{
    /**
     * @param Node|Stmt $node
     * @return string|null
     */
    public static function getClass(mixed $node): string|null
    {
        /** @var FullyQualified|null $classNode */
        $classNode = $node->class;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            $classNode === null => null,
            $classNode instanceof FullyQualified => $classNode->toString(),
            default => dd([
                __FUNCTION__ => $classNode,
            ]),
        };
    }

    public static function getConsts(ClassConst $node): array
    {
        return array_reduce(
            $node->consts,
            static function (array $carry, Const_ $item) {
                /** @var ?string $nodeKey */
                $nodeKey = self::getName($item);
                /** @var mixed $nodeValue */
                $nodeValue = self::getValue($item);

                if (null === $nodeKey) {
                    $carry[] = $nodeValue;
                    return $carry;
                }

                $carry[$nodeKey] = $nodeValue;

                return $carry;
            },
            []
        );
    }

    public static function getDefault(Param|PropertyProperty $node): mixed
    {
        /** @var mixed $node */
        $node = $node->default;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            default => dd([
                __FUNCTION__ => $node,
            ]),
            null === $node => null,
            $node instanceof Array_ => self::getItems($node),
            $node instanceof ConstFetch => self::getName($node),
        };
    }

    public static function getItems(Array_ $node): mixed
    {
        return array_reduce(
            $node->items,
            static function (array $carry, ArrayItem $item) {
                /** @var ?string $nodeKey */
                $nodeKey = self::getKey($item);

                /** @var mixed $nodeValue */
                $nodeValue = self::getValue($item);

                if (null === $nodeKey) {
                    $carry[] = $nodeValue;
                    return $carry;
                }

                $carry[$nodeKey] = $nodeValue;

                return $carry;
            },
            []
        );
    }

    public static function getKey(ArrayItem $node): mixed
    {
        $node = $node->key;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            null === $node => null,
            $node instanceof String_ => self::getValue($node),
            default => dd([
                __FUNCTION__ => $node,
            ]),
        };
    }

    public static function getName(
        Variable|ConstFetch|ClassConstFetch|Const_|Class_|Identifier|ClassMethod|PropertyProperty $node
    ): ?string {
        $node = $node->name;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            null === $node => null,
            is_string($node) => $node,
            $node instanceof Identifier => self::getName($node),
            $node instanceof Name => $node->toString(),
            default => dd([
                __FUNCTION__ => $node,
            ]),
        };
    }

    public static function getParams(ClassMethod $node): array
    {
        return array_reduce(
            $node->params,
            static fn (array $carry, Param $param): array =>
                array_merge($carry, [
                    self::getVar($param) => self::getDefault($param),
                ]),
            []
        );
    }

    public static function getProps(ClassMethod $node): array
    {
        return array_reduce(
            $node->props,
            static fn (array $carry, Param $param): array =>
            array_merge($carry, [
                self::getVar($param) => self::getDefault($param),
            ]),
            []
        );
    }

    public static function getValue(ConstFetch|String_|ClassConstFetch|ArrayItem|Const_ $node): mixed
    {
        /** @var mixed $node */
        $node = $node->value;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            $node instanceof ClassConstFetch => self::getClass($node) . '::' . self::getName($node),
            $node instanceof ConstFetch => self::getName($node),
            $node instanceof String_ => self::getValue($node),
            default => dd([
                __FUNCTION__ => $node,
            ]),
            is_string($node) => $node,
        };
    }

    public static function getVar(Param $node): mixed
    {
        $node = $node->var;
        /** @noinspection ForgottenDebugOutputInspection */
        return match (true) {
            default => dd([
                __FUNCTION__ => $node,
            ]),
            $node instanceof Variable => self::getName($node),
        };
    }
}

function dd(...$value)
{
    var_dump(...$value);
    die;
}
