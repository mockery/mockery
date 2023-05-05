<?php

declare(strict_types=1);

namespace Mockery\Parser\NodeVisitor;

use Mockery\Generator\MockConfiguration;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

abstract class AbstractNodeVisitor extends NodeVisitorAbstract implements NodeVisitor
{
    final public function __construct(
        private readonly MockConfiguration $configuration
    ) {
    }
}
