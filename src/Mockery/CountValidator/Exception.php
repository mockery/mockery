<?php

declare(strict_types=1);

namespace Mockery\CountValidator;

use Mockery\Exception\MockeryExceptionInterface;
use OutOfBoundsException;

class Exception extends OutOfBoundsException implements MockeryExceptionInterface
{
}
