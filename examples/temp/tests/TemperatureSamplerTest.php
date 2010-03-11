<?php

require_once '../../../library/Mockery/Framework.php';
require_once '../library/TemperatureSampler.php';

class TemperatureSamplerTest extends PHPUnit_Framework_TestCase
{

    public function testSensorCanAverageThreeTemperatureReadings()
    {
        $sensor = Mockery::mock('TemperatureSensor');
        $sensor->shouldReceive('readTemperature')->times(3)->andReturn(10, 12, 14);
        $sampler = new TemperatureSampler($sensor);
        $this->assertEquals(12, $sampler->getAverageTemperature());
    }

}

class TemperatureSensor {

public function readTemperature(){}

}
