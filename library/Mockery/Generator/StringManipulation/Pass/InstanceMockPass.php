<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class InstanceMockPass
{
    const INSTANCE_MOCK_CODE = <<<MOCK

    protected \$_mockery_ignoreVerification = true;

    public function __construct()
    {
        \$this->_mockery_ignoreVerification = false;
        \$associatedRealObject = \Mockery::fetchMock(__CLASS__);
        \$directors = \$associatedRealObject->mockery_getExpectations();
        foreach (\$directors as \$method=>\$director) {
            \$expectations = \$director->getExpectations();
            // get the director method needed
            \$existingDirector = \$this->mockery_getExpectationsFor(\$method);
            if (!\$existingDirector) {
                \$existingDirector = new \Mockery\ExpectationDirector(\$method, \$this);
                \$this->mockery_setExpectationsFor(\$method, \$existingDirector);
            }
            foreach (\$expectations as \$expectation) {
                \$clonedExpectation = clone \$expectation;
                \$existingDirector->addExpectation(\$clonedExpectation);
            }
        }
        \Mockery::getContainer()->rememberMock(\$this);
    }
MOCK;

    public function apply($code, MockConfiguration $config)
    {
        if ($config->isInstanceMock()) {
            $code = $this->appendToClass($code, static::INSTANCE_MOCK_CODE);
        }

        return $code;
    }

    protected function appendToClass($class, $code)
    {
        $lastBrace = strrpos($class, "}");
        $class = substr($class, 0, $lastBrace) . $code . "\n    }\n";
        return $class;
    }
}
