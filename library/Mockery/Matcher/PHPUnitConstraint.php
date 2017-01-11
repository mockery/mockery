<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Matcher;

class PHPUnitConstraint extends MatcherAbstract
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
