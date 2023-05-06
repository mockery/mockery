<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use Mockery\Generator\Method;
use Mockery\Parser\NodeExtractor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;

final class AvoidMethodClashVisitor extends AbstractNodeVisitor
{
    private bool $mocksAllowsOrExpectsMethod = false;

    public function beforeTraverse(array $nodes)
    {
        if ($this->mocksAllowsOrExpectsMethod)
        {
            return null;
        }

        /** @var array<Method> $methodsToMock */
        $methodsToMock = $this->configuration->getMethodsToMock();
        foreach ($methodsToMock as $method){
            $methodName = $method->getName();
            if ($methodName === 'allows' || $methodName === 'expects') {
                $this->mocksAllowsOrExpectsMethod = true;

                return null;
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if (! $this->mocksAllowsOrExpectsMethod)
        {
            return null;
        }

        return match (true) {
            $node instanceof ClassMethod => (static function (ClassMethod $node): int|Node {
                if (in_array(NodeExtractor::getName($node), ['allows', 'expects']))
                {
                    return NodeTraverser::REMOVE_NODE;
                }

                return $node;
            })($node),

            $node instanceof Class_ => (static function (Class_ $node): Node {
                foreach ($node->implements as &$interface) {
                    if ($interface instanceof Name && $interface->toString() === 'MockInterface') {
                        $interface = new Name('LegacyMockInterface');
                    }
                }
                return $node;
            })($node),

            default => null,
        };
    }
}
