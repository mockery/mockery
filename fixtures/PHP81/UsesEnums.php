<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace PHP81;

class UsesEnums
{
    public SimpleEnum $enum = SimpleEnum::first;

    public function set(SimpleEnum $enum = SimpleEnum::second)
    {
        $this->enum = $enum;
    }
}
