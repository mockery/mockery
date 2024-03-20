<?php

declare(strict_types=1);

namespace Fixture\PHP74\Regression\Issue1402;

class Service {

    use InitTrait;

    private int $arg;

    public function __construct(int $arg)
    {
        $this->arg = $arg;

        $this->init();
    }

    public function test(): int
    {
        return $this->arg;
    }
}
