<?php

declare(strict_types=1);

namespace Mockery\Parser;

use Generator;
use PhpParser\Node\Stmt;

interface ParserInterface
{
    /**
     * @param string $code
     * @return Stmt[]|null
     */
    public function parse(string $code): array|null;
}
