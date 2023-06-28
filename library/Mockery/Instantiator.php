<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;
use ReflectionClass;
use UnexpectedValueException;
use InvalidArgumentException;

/**
 * This is a trimmed down version of https://github.com/doctrine/instantiator,
 * basically without the caching
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
final class Instantiator
{
    /**
     * {@inheritDoc}
     */
    public function instantiate($className)
    {
        $factory    = $this->buildFactory($className);
        $instance   = $factory();

        return $instance;
    }

    /**
     * Builds a {@see \Closure} capable of instantiating the given $className without
     * invoking its constructor.
     *
     * @param string $className
     *
     * @return Closure
     */
    private function buildFactory($className)
    {
        $reflectionClass = $this->getReflectionClass($className);

        if ($this->isInstantiableViaReflection($reflectionClass)) {
            return function () use ($reflectionClass) {
                return $reflectionClass->newInstanceWithoutConstructor();
            };
        }

        $serializedString = sprintf(
            'O:%d:"%s":0:{}',
            strlen($className),
            $className
        );

        $this->attemptInstantiationViaUnSerialization($reflectionClass, $serializedString);

        return function () use ($serializedString) {
            return unserialize($serializedString);
        };
    }

    /**
     * @param string $className
     *
     * @return ReflectionClass
     *
     * @throws InvalidArgumentException
     */
    private function getReflectionClass($className)
    {
        if (! class_exists($className)) {
            throw new InvalidArgumentException("Class:$className does not exist");
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isAbstract()) {
            throw new InvalidArgumentException("Class:$className is an abstract class");
        }

        return $reflection;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string          $serializedString
     *
     * @throws UnexpectedValueException
     *
     * @return void
     */
    private function attemptInstantiationViaUnSerialization(ReflectionClass $reflectionClass, $serializedString)
    {
        set_error_handler(function ($code, $message, $file, $line) use ($reflectionClass, & $error) {
            $msg = sprintf(
                'Could not produce an instance of "%s" via un-serialization, since an error was triggered in file "%s" at line "%d"',
                $reflectionClass->getName(),
                $file,
                $line
            );

            $error = new UnexpectedValueException($msg, 0, new \Exception($message, $code));
        });

        try {
            unserialize($serializedString);
        } catch (\Exception $exception) {
            restore_error_handler();

            throw new UnexpectedValueException("An exception was raised while trying to instantiate an instance of \"{$reflectionClass->getName()}\" via un-serialization", 0, $exception);
        }

        restore_error_handler();

        if ($error) {
            throw $error;
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     *
     * @return bool
     */
    private function isInstantiableViaReflection(ReflectionClass $reflectionClass)
    {
        return ! ($reflectionClass->isInternal() && $reflectionClass->isFinal());
    }

    /**
     * Verifies whether the given class is to be considered internal
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return bool
     */
    private function hasInternalAncestors(ReflectionClass $reflectionClass)
    {
        do {
            if ($reflectionClass->isInternal()) {
                return true;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return false;
    }
}
