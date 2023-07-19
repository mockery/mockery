<?php

namespace test\Mockery;

trait RegExpCompatability
{
    public function expectExceptionMessageRegEx($regularExpression)
    {
        if (method_exists(get_parent_class($this), 'expectExceptionMessageRegExp')) {
            return parent::expectExceptionMessageRegExp($regularExpression);
        }

        return $this->expectExceptionMessageMatches($regularExpression);
    }

    public static function assertMatchesRegEx($pattern, $string, $message = '')
    {
        if (method_exists(get_parent_class(static::class), 'assertMatchesRegularExpression')) {
            return parent::assertMatchesRegularExpression($pattern, $string, $message);
        }

        return self::assertRegExp($pattern, $string, $message);
    }
}
