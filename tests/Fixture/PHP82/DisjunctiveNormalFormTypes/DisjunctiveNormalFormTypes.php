<?php

declare(strict_types=1);

namespace Fixture\PHP82\DisjunctiveNormalFormTypes;

interface A {}
interface B {}
interface C extends A {}
interface D {}

class W implements A {}
class X implements B {}
class Y implements A, B {}
class Z extends Y implements C {}


//===
//Return co-variance
//When extending a class, a method return type may narrow only. That is, it must be the same or more restrictive as its parent. In practice, that means additional ANDs may be added, but not additional ORs.
//===
interface ITest {
    public function stuff(): (A&B)|D;
}

// Acceptable.  A&B is more restrictive.
class TestOne implements ITest {
    public function stuff(): A&B {}
}

// Acceptable. D is is a subset of A&B|D
class TestTwo implements ITest {
    public function stuff(): D {}
}

// Acceptable, since C is a subset of A&B,
// even though it is not identical.
class TestThree implements ITest {
    public function stuff(): C|D {}
}

// Not acceptable. This would allow an object that
//  implements A but not B, which is wider than the interface.
class TestFour implements ITest {
    public function stuff(): A|D {}
}

interface ITestTwo {
    public function things(): C|D {}
}

// Not acceptable. Although C extends A and B, it's possible
// for an object to implement A and B without implementing C.
// Thus this definition is wider, and not allowed.
class TestFive implements ITestTwo {
    public function things(): (A&B)|D {}
}


//===
//Parameter contra-variance
//When extending a class, a method parameter type may widen only. That is, it must be the same or less restrictive as its parent. In practice, that means additional ORs may be added, but not additional ANDs.

interface ITest {
    public function stuff((A&B)|D $arg): void {}
}

// Acceptable. Everything that ITest accepts is still valid
// and then some.
class TestOne implements ITest {
    public function stuff((A&B)|D|Z $arg): void {}
}

// Acceptable. This accepts objects that implement just
// A, which is a super-set of those that implement A&B.
class TestOne implements ITest {
    public function stuff(A|D $arg): void {}
}

// Not acceptable. The interface says D is acceptable,
// but this class does not.
class TestOne implements ITest {
    public function stuff((A&B) $arg): void {}
}

interface ITestTwo {
    public function things(C|D $arg): void;
}

// Acceptable. Anything that implements C implements A&B,
// but this rule also allows classes that implement A&B
// directly, and thus is wider.
class TestFive implements ITestTwo {
    public function things((A&B)|D $arg): void;
}
