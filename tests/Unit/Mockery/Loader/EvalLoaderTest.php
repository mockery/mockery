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

use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;

/**
 * @covers \Mockery\Loader\EvalLoader
 * @internal
 */
final class EvalLoaderTest extends AbstractLoaderTestCase
{
    public function getLoader(): Loader
    {
        return new EvalLoader();
    }
}
