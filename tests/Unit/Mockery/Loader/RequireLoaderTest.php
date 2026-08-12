<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Loader;

use Mockery\Loader\Loader;
use Mockery\Loader\RequireLoader;
use Override;

use function sys_get_temp_dir;

/**
 * @coversDefaultClass \Mockery
 */
final class RequireLoaderTest extends LoaderTestCase
{
    #[Override]
    public function getLoader(): Loader
    {
        return new RequireLoader(sys_get_temp_dir());
    }
}
