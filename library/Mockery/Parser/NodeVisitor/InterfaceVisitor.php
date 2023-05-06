<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use Mockery;
use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\UndefinedTargetClass;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

final class InterfaceVisitor extends AbstractNodeVisitor
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof Class_) {
            return null;
        }

        /** @var UndefinedTargetClass|DefinedTargetClass $targetInterface */
        foreach ($this->configuration->getTargetInterfaces() as $targetInterface) {
            $name = ltrim($targetInterface->getName(), '\\');
            if (! interface_exists($name)) {
                Mockery::declareInterface($name);
            }
            $node->implements[] = new Node\Name('\\' . $name);
        }

        return $node;
    }
}
