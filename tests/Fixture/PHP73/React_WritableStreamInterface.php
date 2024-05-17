<?php

declare(strict_types=1);

namespace PHP73;

interface React_WritableStreamInterface extends React_StreamInterface
{
    public function write($data);
}
