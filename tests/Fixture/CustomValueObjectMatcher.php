<?php

namespace MockeryTest\Fixture;

class CustomValueObjectMatcher extends \Mockery\Matcher\MatcherAbstract
{
    public function match(&$actual)
    {
        return $actual->value === $this->_expected->value;
    }
    public function __toString()
    {
        return "<customMatcher>";
    }
}
