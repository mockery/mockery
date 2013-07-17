<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\Method;

class MethodDefinitionPass implements Pass
{
    /**
     * Purpose of this block is to create an argument array where
     * references are preserved (func_get_args() does not preserve
     * references)
     */
    const METHOD_BODY = <<<BODY
{
\$stack = debug_backtrace();
\$args = array();
if (isset(\$stack[0]['args'])) {
    for(\$i=0; \$i<count(\$stack[0]['args']); \$i++) {
        \$args[\$i] =& \$stack[0]['args'][\$i];
    }
}
\$ret = \$this->__call(__FUNCTION__, \$args);
return \$ret;
}
BODY;

    const STATIC_METHOD_BODY = <<<BODY
{
\$stack = debug_backtrace();
\$args = array();
if (isset(\$stack[0]['args'])) {
    for(\$i=0; \$i<count(\$stack[0]['args']); \$i++) {
        \$args[\$i] =& \$stack[0]['args'][\$i];
    }
}
\$ret = static::__callStatic(__FUNCTION__, \$args);
return \$ret;
}

BODY;

    public function apply($code, MockConfiguration $config)
    {
        foreach ($config->getMethodsToMock() as $method) {

            if ($method->isPublic()) {
                $methodDef = 'public';
            } elseif($method->isProtected()) {
                $methodDef = 'protected';
            } else {
                $methodDef = 'private';
            }

            if ($method->isStatic()) {
                $methodDef .= ' static';
            }

            $methodDef .= ' function ';
            $methodDef .= $method->returnsReference() ? ' & ' : '';
            $methodDef .= $method->getName();
            $methodDef .= $this->renderParams($method, $config);
            $methodDef .= $method->isStatic() ? static::STATIC_METHOD_BODY : static::METHOD_BODY;
            
            $code = $this->appendToClass($code, $methodDef);
        }

        return $code;
    }

    protected function renderParams(Method $method, $config)
    {
        $class = $method->getDeclaringClass(); 
        if ($class->isInternal()) { 
            $overrides = $config->getParameterOverrides();

            if (isset($overrides[strtolower($class->getName())][$method->getName()])) {
                return '(' . implode(',', $overrides[strtolower($class->getName())][$method->getName()]) . ')';
            }
        }

        $methodParams = array();
        $params = $method->getParameters();
        foreach ($params as $param) {
            $paramDef = $param->getTypeHintAsString();
            $paramDef .= $param->isPassedByReference() ? '&' : '';
            $paramDef .= '$' . $param->getName();

            if ($param->isOptional()) {
                if ($param->isDefaultValueAvailable()) {
                    $default = var_export($param->getDefaultValue(), true);
                } else {
                    $default = 'null';
                }
                $paramDef .= ' = ' . $default;
            }

            $methodParams[] = $paramDef;
        }
        return '(' . implode(', ', $methodParams) . ')';
    }

    protected function appendToClass($class, $code)
    {
        $lastBrace = strrpos($class, "}");
        $class = substr($class, 0, $lastBrace) . $code . "\n    }\n";
        return $class;
    }
}
