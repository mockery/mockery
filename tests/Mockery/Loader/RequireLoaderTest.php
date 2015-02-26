<?php

namespace Mockery\Loader;

require_once __DIR__ . '/LoaderTestCase.php';

class RequireLoaderTest extends LoaderTestCase
{
    public function getLoader()
    {
        return new RequireLoader(sys_get_temp_dir());
    }
}
