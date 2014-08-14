<?php
/**
 * @author Boris Avdeev <elephant@lislon.ru>
 */
namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

/**
 * Remove mock's empty destructor if we tend to use original class destructor
 */
class RemoveDestructorPass
{
    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (!$target) {
            return $code;
        }

        if (!$config->isMockOriginalDestructor()) {
            $code = preg_replace('/public function __destruct\(\)\s+\{.*?\}/sm', '', $code);
        }

        return $code;
    }
}
