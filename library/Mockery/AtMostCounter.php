<?php

class Mockery_AtMostCounter
{

    protected $_times = 1;

    public function __construct($times)
    {
        $this->_times = $times;
    }

    public function verify($callTimesActual)
    {
        return $callTimesActual <= $this->_times;
    }

    public function getDescription()
    {
        return 'at most ' . $this->_times . ($this->_times !== 1 ? ' times' : ' time');
    }

}
