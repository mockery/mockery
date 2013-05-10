<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class ClassPass implements Pass
{
    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (!$target) {
            return $code;
        }

        if ($target->isFinal()) {
            return $code;
        }

        $className = ltrim($target->getName(), "\\");
        if (!class_exists($className)) {

            $targetCode = '<?php ';

            if ($target->inNamespace()) {
                $targetCode.= 'namespace ' . $target->getNamespaceName(). '; ';
            }

            $targetCode.= 'class ' . $target->getShortName() . ' {} ';

            eval('?>' . $targetCode);
        }

        $code = str_replace(
            "implements MockInterface",
            "extends \\" . $className . " implements MockInterface",
            $code
        );

        return $code;
    }
}
