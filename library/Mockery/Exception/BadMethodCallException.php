<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Exception;

use Throwable;

class BadMethodCallException extends \BadMethodCallException implements MockeryExceptionInterface
{
    private $dismissed = false;

    public function dismiss()
    {
        $this->dismissed = true;
        // we sometimes stack them
        $previous = $this->getPrevious();
        if (! $previous instanceof self) {
            return;
        }

        $previous->dismiss();
    }

    public function dismissed()
    {
        return $this->dismissed;
    }
}
