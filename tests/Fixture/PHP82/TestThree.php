<?php

declare(strict_types=1);

namespace PHP82;

class TestThree implements ITestTwo
{

// Anything that implements C implements A&B,
// but this rule also allows classes that implement A&B
// directly, and thus is wider.
    public function things((AInterface&BInterface)|DInterface $arg): void
    {

    }
}
