<?php

class MockMe_Methods
{

    public function shouldReceive($methodName)
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        $directors = $store->directors;
        if (!isset($directors[$methodName])) {
            $directors[$methodName] = new MockMe_Director($methodName);
        }
        $expectation = new MockMe_Expectation($methodName, $this);
        $directors[$methodName]->addExpectation($expectation);
        $store->directors = $directors;
        return $expectation;
    }

    public function mockme_verify()
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        if ($store->verified) {
            return $store->verified;
        }
        $store->verified = true;
        foreach ($store->directors as $director) {
            $director->verify();
        }
        return $store->verified;
    }

    public function mockme_setVerifiedStatus($bool)
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        $store->verified = $bool;
    }

    public function mockme_call($methodName, $args)
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        $return = null;
        $directors = $store->directors;
        if (!isset($directors[$methodName])) {//
            $directors[$methodName] = new MockMe_Director($methodName);
            $expectation = new MockMe_Expectation($methodName, $this);
            $directors[$methodName]->addExpectation($expectation);
            $expectation->never();
        }//
        $return = $directors[$methodName]->call($args, $this);
        return $return;
    }

    public function mockme_getOrderedNumberNext()
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        $orderedNumberNext = $store->orderedNumberNext;
        $orderedNumberNext++;
        $store->orderedNumberNext = $orderedNumberNext;
        return $orderedNumberNext;
    }

    public function mockme_getOrderedNumber()
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        return $store->orderedNumber;
    }

    public function mockme_incrementOrderedNumber()
    {
        $store = MockMe_Store::getInstance(spl_object_hash($this));
        $orderedNumber = $store->orderedNumber;
        $orderedNumber++;
        $store->orderedNumber = $orderedNumber;
    }

}
