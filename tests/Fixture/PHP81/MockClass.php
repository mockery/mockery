<?php

declare(strict_types=1);

namespace Fixture\PHP81;

use DateTime;

class MockClass
{
    public function test(
        string $msg,
        A $a = new A(),
        B $b = new B(1),
        C $c = new C(x: 2),
        DateTime $dateTime = new DateTime(),
    ): string {
        return $msg . ' - ' . $a->test() . ' - ' . $b->test() . ' - ' . $c->test() . ' - ' . $dateTime->format('Y-m-d');
    }
}
