<?php

declare(strict_types=1);

namespace Mockery\Adapter\Phpunit;

use Mockery;
use PHPUnit\Util\ExcludeList;
use ReflectionClass;

final class ExtensionTest extends MockeryTestCase
{
    public function testMockeryIsAddedToExcludeList(): void
    {
        static::assertTrue(
            (new ExcludeList())->isExcluded(
                (new ReflectionClass(Mockery::class))->getFileName()
            )
        );
    }
}
