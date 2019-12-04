<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;

class RemoveCommentsFromMockPass implements Pass
{
    private $strippedCode;

    private $tokens;

    /**
     * @param string $code
     * @param MockConfiguration $config
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        if ($this->strippedCode !== null) {
            return $this->strippedCode;
        }

        $this->strippedCode = '';

        $this->removeComments($code);

        $this->removeEmptyLines();

        return $this->strippedCode;
    }

    /**
     * @return string
     */
    private function removeEmptyLines()
    {
        $this->strippedCode = preg_replace('#(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#', "\n", $this->strippedCode);
    }

    /**
     * @param string $code
     */
    private function removeComments($code)
    {
        $tokens = token_get_all($code);

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_DOC_COMMENT || $token[0] == T_COMMENT) {
                    continue;
                }

                $token = $token[1];
            }
            $this->strippedCode .= $token;
        }
    }
}