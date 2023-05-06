<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

final class CallTypeHintVisitor extends AbstractNodeVisitor
{
    private bool $requiresCallTypeHintRemoval = false;
    private bool $requiresCallStaticTypeHintRemoval = false;

    public function beforeTraverse(array $nodes)
    {
        $this->requiresCallTypeHintRemoval = (bool) $this->configuration->requiresCallTypeHintRemoval();
        $this->requiresCallStaticTypeHintRemoval = (bool) $this->configuration->requiresCallStaticTypeHintRemoval();
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }

        if (!$this->requiresCallTypeHintRemoval && !$this->requiresCallStaticTypeHintRemoval) {
            return null;
        }

        foreach ($node->params as &$param) {
            $param->type = null;
        }

        return $node;
    }
}
