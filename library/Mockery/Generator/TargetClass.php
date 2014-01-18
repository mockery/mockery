<?php

namespace Mockery\Generator;

interface TargetClass
{
    /** @return string */
    function getName();

    /** @return bool */
    function isAbstract();

    /** @return bool */
    function isFinal();

    /** @return Method[] */
    function getMethods();

    /** @return string */
    function getNamespaceName();

    /** @return bool */
    function inNamespace();

    /** @return string */
    function getShortName();

}
