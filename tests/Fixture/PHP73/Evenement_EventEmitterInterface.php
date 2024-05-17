<?php

declare(strict_types=1);

namespace PHP73;

interface Evenement_EventEmitterInterface
{
    public function on($name, $callback);
}
