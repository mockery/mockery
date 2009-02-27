<?php

class MockMe_StandardCounter
{

    protected $_times = 1;

    public function __construct($times)
    {
        $this->_times = $times;
    }

    public function verify($callTimesActual)
    {
        if ($this->_times == $callTimesActual) {
            return true;
        }
        return false;
    }

    public function getDescription()
    {
        return $this->_times . ($this->_times !== 1 ? ' times' : ' time');
    }

}
