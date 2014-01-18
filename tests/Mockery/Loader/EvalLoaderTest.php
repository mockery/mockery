<?php

namespace Mockery\Loader;

use Mockery as m;
use Mockery\Loader\EvalLoader;

require_once __DIR__.'/LoaderTestCase.php';

class EvalLoaderTest extends LoaderTestCase
{
    public function getLoader()
    {
        return new EvalLoader();
    }
}
