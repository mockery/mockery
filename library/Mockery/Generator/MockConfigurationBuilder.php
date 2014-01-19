<?php

namespace Mockery\Generator;

class MockConfigurationBuilder
{
    protected $name;
    protected $blackListedMethods = array(
        '__call',
        '__callStatic',
        '__clone',
        '__wakeup',
        '__set',
        '__get',
        '__toString',
        '__isset',
        '__destruct',

        // below are reserved words in PHP
        "__halt_compiler", "abstract", "and", "array", "as",
        "break", "callable", "case", "catch", "class",
        "clone", "const", "continue", "declare", "default",
        "die", "do", "echo", "else", "elseif",
        "empty", "enddeclare", "endfor", "endforeach", "endif",
        "endswitch", "endwhile", "eval", "exit", "extends",
        "final", "for", "foreach", "function", "global",
        "goto", "if", "implements", "include", "include_once",
        "instanceof", "insteadof", "interface", "isset", "list",
        "namespace", "new", "or", "print", "private",
        "protected", "public", "require", "require_once", "return",
        "static", "switch", "throw", "trait", "try",
        "unset", "use", "var", "while", "xor"
    );
    protected $whiteListedMethods = array();
    protected $instanceMock = false;
    protected $parameterOverrides = array();

    protected $targets = array();

    public function addTarget($target)
    {
        $this->targets[] = $target;

        return $this;
    }

    public function addTargets($targets)
    {
        foreach ($targets as $target) {
            $this->addTarget($target);
        }

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function addBlackListedMethod($blackListedMethod)
    {
        $this->blackListedMethods[] = $blackListedMethod;
        return $this;
    }

    public function addBlackListedMethods(array $blackListedMethods)
    {
        foreach ($blackListedMethods as $method) {
            $this->addBlackListedMethod($method);
        }
        return $this;
    }

    public function setBlackListedMethods(array $blackListedMethods)
    {
        $this->blackListedMethods = $blackListedMethods;
        return $this;
    }

    public function addWhiteListedMethod($whiteListedMethod)
    {
        $this->whiteListedMethods[] = $whiteListedMethod;
        return $this;
    }

    public function addWhiteListedMethods(array $whiteListedMethods)
    {
        foreach ($whiteListedMethods as $method) {
            $this->addWhiteListedMethod($method);
        }
        return $this;
    }

    public function setWhiteListedMethods(array $whiteListedMethods)
    {
        $this->whiteListedMethods = $whiteListedMethods;
        return $this;
    }

    public function setInstanceMock($instanceMock)
    {
        $this->instanceMock = (bool) $instanceMock;
    }

    public function setParameterOverrides(array $overrides)
    {
        $this->parameterOverrides = $overrides;
    }

    public function getMockConfiguration()
    {
        return new MockConfiguration(
            $this->targets,
            $this->blackListedMethods,
            $this->whiteListedMethods,
            $this->name,
            $this->instanceMock,
            $this->parameterOverrides
        );
    }

}
