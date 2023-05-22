<?php

declare(strict_types=1);

namespace Mockery\CountValidator;

use Mockery;

final class AtMost extends AbstractCountValidator
{
    /**
     * Validate the call count against this validator
     */
    public function validate(int $n): bool
    {
        if ($this->limit < $n) {
            $exception = new Mockery\Exception\InvalidCountException(
                'Method ' . (string) $this->expectation
                . ' from ' . $this->expectation->getMock()->mockery_getName()
                . ' should be called' . PHP_EOL
                . ' at most ' . $this->limit . ' times but called ' . $n
                . ' times.'
            );
            $exception->setMock($this->expectation->getMock())
                ->setMethodName((string) $this->expectation)
                ->setExpectedCountComparative('<=')
                ->setExpectedCount($this->limit)
                ->setActualCount($n);
            throw $exception;
        }

        return true;
    }
}
