<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

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
