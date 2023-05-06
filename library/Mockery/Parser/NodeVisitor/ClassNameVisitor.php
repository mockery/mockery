<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

final class ClassNameVisitor extends AbstractNodeVisitor
{
    public function leaveNode(Node $node)
    {
        $config = $this->configuration;

        return match (true) {
            $node instanceof Namespace_ => (static function (Namespace_ $node) use ($config): Namespace_ {
                /** @var string $namespace */
                $namespace = $config->getNamespaceName();

                $node->name = $namespace === '' ? null : new Name($namespace);

                return $node;
            })($node),

            $node instanceof Class_ => (static function (Class_ $node) use ($config): Class_ {
                $node->name = new Identifier((string) $config->getShortName());

                return $node;
            })($node),

            default => null,
        };
    }
}
