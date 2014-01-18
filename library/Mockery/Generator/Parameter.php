<?php

namespace Mockery\Generator;

class Parameter
{
    private static $parameterCounter;

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
        if (method_exists($this->rfp, 'getTypehintText')) {
            // Available in HHVM
            return $this->rfp->getTypehintText();
        }

        if ($this->rfp->isArray()) {
            return 'array';
        }

        /*
         * PHP < 5.4.1 has some strange behaviour with a typehint of self and
         * subclass signatures, so we risk the regexp instead
         */
        if ((version_compare(PHP_VERSION, '5.4.1') >= 0)) {
            try {
                if ($this->rfp->getClass()) {
                    return $this->rfp->getClass()->getName();
                }
            } catch (\ReflectionException $re) {
                // noop
            }
        }

        if (preg_match('/^Parameter #[0-9]+ \[ \<(required|optional)\> (?<typehint>\S+ )?.*\$' . $this->rfp->getName() . ' .*\]$/', $this->rfp->__toString(), $typehintMatch)) {
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
            $name = 'arg' . static::$parameterCounter++;
        }

        return $name;
    }
}
