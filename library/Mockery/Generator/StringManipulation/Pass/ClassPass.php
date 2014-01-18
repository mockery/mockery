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

            /*
             * We could eval here, but it doesn't play well with the way
             * PHPUnit tries to backup global state and the require definition
             * loader
             */
            $tmpfname = tempnam(sys_get_temp_dir(), "Mockery");
            file_put_contents($tmpfname, $targetCode);
            require $tmpfname;
        }

        $code = str_replace(
            "implements MockInterface",
            "extends \\" . $className . " implements MockInterface",
            $code
        );

        return $code;
    }
}
