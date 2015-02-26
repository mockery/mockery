<?php

namespace Mockery\Loader;

require_once __DIR__ . '/LoaderTestCase.php';

class EvalLoaderTest extends LoaderTestCase
{
    public function getLoader()
    {
        return new EvalLoader();
    }
}
