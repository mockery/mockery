<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Loader;

use Mockery\Loader\EvalLoader;
use Mockery\Loader\Loader;

/**
 * @coversDefaultClass \Mockery
 */
final class EvalLoaderTest extends LoaderTestCase
{
    public function getLoader(): Loader
    {
        return new EvalLoader();
    }
}
