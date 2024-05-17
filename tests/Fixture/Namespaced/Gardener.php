<?php

declare(strict_types=1);

namespace
{
    abstract class Gardener
    {
        abstract public function water(Nature\Plant $plant);
    }
}
