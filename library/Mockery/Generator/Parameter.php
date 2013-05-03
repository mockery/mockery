<?php

namespace Mockery\Generator;

class Parameter 
{
    private $rfp;

    public function __construct(\ReflectionParameter $rfp)
    {
        $this->rfp = $rfp;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->rfp, $method), $args);
    }

    public function getClass()
    {
        return new DefinedTargetClass($this->rfp->getClass());
    }

    public function getTypeHintAsString()
    {
        if ($this->rfp->isArray()) {
            return 'array';
        }

        if ($this->rfp->getClass()) {
            return $this->rfp->getClass()->getName();
        }

        if (preg_match('/^Parameter #[0-9]+ \[ \<(required|optional)\> (?<typehint>\S+ )?.*\$' . $this->rfp->getName() . ' .*\]$/', $param->__toString(), $typehintMatch)) {
            if (!empty($typehintMatch['typehint'])) {
                return $typehintMatch['typehint'];
            }
        }

        return '';
    }

    /**
     * Some internal classes have funny looking definitions...
     */
    public function getName()
    {
        $name = $this->rfp->getName();
        if (!$name || $name == '...') {
            $name = 'arg' . uniqid();
        }

        return $name;
    }
}
