<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\UndefinedTargetClass;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name;

final class ClassAttributesVisitor extends AbstractNodeVisitor
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof AttributeGroup) {
            return null;
        }

        /** @var  DefinedTargetClass|UndefinedTargetClass|null $class */
        $class = $this->configuration->getTargetClass();
        if ($class === null) {
            return null;
        }

        /** @var array<string> $attributes */
        $attributes = $class->getAttributes();
        if ($attributes === []) {
            return null;
        }

        $node->attrs = array_map(
            static fn (string $attributeName): Attribute => new Attribute(new Name($attributeName)),
            $attributes
        );

        return $node;
    }
}
