<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\MockInterface;

if (! \function_exists('andAnyOtherArgs')) {
    function andAnyOtherArgs(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }
}

if (! \function_exists('andAnyOthers')) {
    function andAnyOthers(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }
}

if (! \function_exists('anyArgs')) {
    function anyArgs(): AnyArgs
    {
        return new AnyArgs();
    }
}

if (! \function_exists('mock')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function mock(...$args)
    {
        return Mockery::mock(...$args);
    }
}

if (! \function_exists('namedMock')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function namedMock(...$args)
    {
        return Mockery::namedMock(...$args);
    }
}

if (! \function_exists('spy')) {
    /**
     * @param  mixed         ...$args
     * @return MockInterface
     *
     * @throws Throwable
     */
    function spy(...$args)
    {
        return Mockery::spy(...$args);
    }
}

if (! \function_exists('array_is_list')) {
    /**
     * Modified copy from https://www.php.net/manual/en/function.array-is-list.php#127044
     *
     * @license https://www.php.net/manual/en/cc.license.php
     *
     * @param array<mixed> $array
     */
    function array_is_list(array $array): bool
    {
        $i = -1;
        foreach ($array as $k => $v) {
            if ($k !== ++$i) {
                return false;
            }
        }

        return true;
    }
}
