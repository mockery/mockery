<?php

namespace Mockery\Generator;

interface TargetClass
{
    /** @return string */
    public function getName();

    /** @return bool */
    public function isAbstract();

    /** @return bool */
    public function isFinal();

    /** @return Method[] */
    public function getMethods();

    /** @return string */
    public function getNamespaceName();

    /** @return bool */
    public function inNamespace();

    /** @return string */
    public function getShortName();

    /** @return bool */
    public function implementsInterface($interface);

    /** @return bool */
    public function hasInternalAncestor();
}
