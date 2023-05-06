<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\UndefinedTargetClass;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

final class ClassVisitor extends AbstractNodeVisitor
{
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Class_){
            return null;
        }

        /** @var DefinedTargetClass|UndefinedTargetClass|null $target */
        $target = $this->configuration->getTargetClass();
        if ($target === null || $target->isFinal()) {
            return null;
        }

        $className = ltrim($target->getName(), "\\");
        if (!class_exists($className)) {
            \Mockery::declareClass($className);
        }

        $node->extends = new Node\Name($className);

        return $node;
    }
}
