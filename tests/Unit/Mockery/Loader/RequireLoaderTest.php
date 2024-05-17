<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Loader;

use Mockery\Loader\Loader;
use Mockery\Loader\RequireLoader;

use function sys_get_temp_dir;

/**
 * @coversDefaultClass \Mockery
 */
final class RequireLoaderTest extends LoaderTestCase
{
    public function getLoader(): Loader
    {
        return new RequireLoader(sys_get_temp_dir());
    }
}
