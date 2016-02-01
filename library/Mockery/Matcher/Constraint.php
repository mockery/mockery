<?php
namespace Mockery\Matcher;

class Constraint extends MatcherAbstract
{
    protected $constraint;
    protected $rethrow;

    /**
     * @param \PHPUnit_Framework_Constraint $constraint
     * @param bool $rethrow
     */
    public function __construct(\PHPUnit_Framework_Constraint $constraint, $rethrow = false)
    {
        $this->constraint = $constraint;
        $this->rethrow = $rethrow;
    }

    /**
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        try {
            $this->constraint->evaluate($actual);
            return true;
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            if ($this->rethrow) {
                throw $e;
            }
            return false;
        }
    }

    /**
     *
     */
    public function __toString()
    {
        return '<Constraint>';
    }
}
