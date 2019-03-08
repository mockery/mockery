<?php

namespace Mockery\Generator;
{
    class ParameterTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         */
        public function shouldReturnAbsoluteHintPathIfNamespaceProvided()
        {
            $rfp = new \ReflectionParameter(array('\TestSubjectNameSpace\TestSubject', 'foo'), 0);
            $parameterGenerator = new Parameter($rfp);
            $typeHint = $parameterGenerator->getTypeHintAsString();

            $this->assertEquals('\TypeHintNameSpace\TypeHint', $typeHint);
        }
    }
}

namespace TypeHintNameSpace;
{
    class TypeHint
    {
    }
}

namespace TestSubjectNameSpace;
use TypeHintNameSpace\TypeHint;
{
    class TestSubject
    {
        public function foo(TypeHint $param)
        {
        }
    }
}


