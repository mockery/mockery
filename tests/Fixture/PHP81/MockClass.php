<?php

declare(strict_types=1);

namespace Fixture\PHP81;

use DateTime;

class MockClass
{
    public function __construct(
        public A $a = new A(),
        protected B $b = new B(1),
        private C $c = new C(b: new B(1), x: 2),
        public DateTime $dateTime = new DateTime(),
    ) {
    }

    public function test(
        string $msg,
        #[SomeAttribute(param: new A())]
        A $a = new A(),
        B $b = new B(1),
        C $c = new C(x: 2),
        DateTime $dateTime = new DateTime(),
    ): string {
        return $msg . ' - ' . $a->test() . ' - ' . $b->test() . ' - ' . $c->test() . ' - ' . $dateTime->format('Y-m-d');
    }
}
