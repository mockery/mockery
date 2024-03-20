<?php

declare(strict_types=1);

namespace Fixture\PHP74\Regression\Issue1402;

/**
 * This trait does something, but we need to initialise a thing on construction.
 */
trait InitTrait {

    protected function init(): void {}

}
