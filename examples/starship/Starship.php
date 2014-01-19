<?php

class Starship
{

    protected $_engineering = null;

    public function __construct($engineering)
    {
        $this->_engineering = $engineering;
    }

    public function enterOrbit()
    {
        $this->_engineering->disengageWarp();
        $this->_engineering->runDiagnosticLevel(5);
        $this->_engineering->divertPower(0.40, 'sensors');
        $this->_engineering->divertPower(0.30, 'auxengines');
        $this->_engineering->runDiagnosticLevel(1);

        // We can add more runDiagnosticLevel() calls without failing the test
        // anywhere above since they are unordered.
    }

}
