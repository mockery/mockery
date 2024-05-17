<?php

declare(strict_types=1);

namespace PHP82;

class TestTwo implements ITest
{
    // Acceptable. This accepts objects that implement just
    // A, which is a super-set of those that implement A&B.
    public function stuff(AInterface|DInterface $arg): void
    {
    }
}
