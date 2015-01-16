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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Matcher;

class NestedSubset extends MatcherAbstract
{

    /**
     * Check if the actual value matches the expected.
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        foreach ($this->_expected as $k=>$v) {
            if (!$this->matchNested($actual, $k, $v)) {
                return false;
            }
        }
        return true;
    }

    private function matchNested($actual, $expected_k, $expected_v) {
        if (!is_array($expected_v)) {
            $to_compare = is_array($actual) ? $actual[$expected_k] : $actual;
            return $expected_v === $to_compare;
        }

        if (!is_array($actual) && !is_array($expected_v)) {
            return $actual === $expected_v;
        }
        if (!array_key_exists($expected_k, $actual)) {
            return false;
        }

        foreach ($expected_v as $k => $v) {
            if (!array_key_exists($k, $actual[$expected_k])) {
                return false;
            }
            $result = $this->matchNested($actual[$expected_k], $k, $v);
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        $return = '<Subset[';
        $elements = array();
        foreach ($this->_expected as $k=>$v) {
            $elements[] = $k . '=' . (string) $v;
        }
        $return .= implode(', ', $elements) . ']>';
        return $return;
    }

}
