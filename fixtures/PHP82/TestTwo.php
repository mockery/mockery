<?php

namespace PHP82;

// Acceptable. This accepts objects that implement just
// A, which is a super-set of those that implement A&B.
class TestTwo implements ITest
{
    public function stuff(A|D $arg): void
    {
    }
}
