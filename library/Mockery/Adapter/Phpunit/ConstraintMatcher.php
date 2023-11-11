<?php

namespace Mockery\Adapter\Phpunit;

use Mockery\Matcher\Matcher;
use PHPUnit\Framework\Constraint\Constraint;

class ConstraintMatcher implements Matcher
{
    /** @var Constraint */
    private $constraint;

    public function __construct(Constraint $constraint)
    {
        $this->constraint = $constraint;
    }

    public function match(&$actual)
    {
        return (bool) $this->constraint->evaluate($actual, '', true);
    }

    public function __toString()
    {
        return '<PhpUnitConstraint[' . $this->constraint->toString() . ']>';
    }
}
