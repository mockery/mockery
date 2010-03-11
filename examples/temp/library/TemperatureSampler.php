<?php

class TemperatureSampler
{

    public $sensor = null;

    public function __construct($sensor)
    {
        $this->sensor = $sensor;
    }
    
    public function getAverageTemperature()
    {
        $total = 0;
        for ($i = 3; $i > 0; $i--) {
            $total += $this->sensor->readTemperature();
        }
        return $total / 3;
    }

}
