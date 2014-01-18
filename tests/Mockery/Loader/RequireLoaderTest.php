<?php

namespace Mockery\Loader;

use Mockery as m;
use Mockery\Loader\RequireLoader;

require_once __DIR__.'/LoaderTestCase.php';

class RequireLoaderTest extends LoaderTestCase
{
    public function getLoader()
    {
        return new RequireLoader(sys_get_temp_dir());
    }
}
