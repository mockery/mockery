<?php

class MockMe_ZeroOrMoreCounter
{

    public function verify($callTimesActual)
    {
        return true;
    }

    public function getDescription()
    {
        return 'zero or more times';
    }

}
