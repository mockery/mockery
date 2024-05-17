<?php

declare(strict_types=1);

namespace PHP73;

interface React_StreamInterface extends Evenement_EventEmitterInterface
{
    public function close();
}
