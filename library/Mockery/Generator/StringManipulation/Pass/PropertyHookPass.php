<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Exception\InvalidArgumentException;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\PropertyHook;
use Override;

use const PHP_VERSION_ID;

use function array_filter;
use function implode;
use function sprintf;
use function strrpos;
use function substr;

/**
 * @see PropertyHookPassTest
 */
final class PropertyHookPass implements Pass
{
    /**
     * @param  string $code
     * @return string
     *
     * @throws InvalidArgumentException
     */
    #[Override]
    public function apply($code, MockConfiguration $config)
    {
        if (PHP_VERSION_ID < 80400) {
            return $code;
        }

        $propertyHooks = $config->getPropertyHooksToMock();
        if (empty($propertyHooks)) {
            return $code;
        }

        $members = '';

        foreach ($propertyHooks as $propertyHook) {
            $members .= $this->renderPropertyHook($propertyHook);
        }

        return $this->appendToClass($code, $members);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function appendToClass(string $class, string $code): string
    {
        $lastBrace = strrpos($class, '}');

        if (false === $lastBrace) {
            throw new InvalidArgumentException('Invalid class code: no closing brace found');
        }

        return implode("\n", [substr($class, 0, $lastBrace), $code, '', '}', '']);
    }

    private function renderPropertyGetHook(PropertyHook $hook): string
    {
        $indent = '    ';

        return implode("\n", [
            $indent . $indent . 'get {',
            $indent . $indent . $indent . sprintf(
                "return \$this->_mockery_handleMethodCall('\$%s::get', []);",
                $hook->getName()
            ),
            $indent . $indent . '}',
        ]);
    }

    private function renderPropertyHook(PropertyHook $hook): string
    {
        $indent = '    ';

        return implode("\n", array_filter([
            '',
            $indent . sprintf(
                '%s %s$%s {',
                $hook->getVisibility(),
                $this->renderPropertyType($hook),
                $hook->getName()
            ),
            $hook->hasGetHook() ? $this->renderPropertyGetHook($hook) : null,
            $hook->hasSetHook() ? $this->renderPropertySetHook($hook) : null,
            $indent . '}',
        ]));
    }

    private function renderPropertySetHook(PropertyHook $hook): string
    {
        $indent = '    ';

        return implode("\n", [
            $indent . $indent . 'set {',
            $indent . $indent . $indent . sprintf(
                "\$this->_mockery_handleMethodCall('\$%s::set', [\$value]);",
                $hook->getName()
            ),
            $indent . $indent . '}',
        ]);
    }

    private function renderPropertyType(PropertyHook $hook): string
    {
        $type = $hook->getType();
        if (null === $type) {
            return '';
        }

        return $type . ' ';
    }
}
