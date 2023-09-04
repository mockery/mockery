<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Tests\Unit\Mockery\Loader;

use Mockery\Loader\Loader;
use Mockery\Loader\RequireLoader;

/**
 * @covers \Mockery\Loader\RequireLoader
 * @internal
 */
final class RequireLoaderTest extends AbstractLoaderTestCase
{
    public function getLoader(): Loader
    {
        return new RequireLoader(sys_get_temp_dir());
    }
}
