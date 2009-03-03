<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/MockMe/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockmeExpectationsTest extends PHPUnit_Framework_TestCase
{

    public function testShouldReturnTrueOnValidationByDefaultIfNoExpectationsSet()
    {
        $mock = mockme('MockMeTest_EmptyClass');
        try {
            $this->assertTrue($mock->mockme_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }

    public function testShouldThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName');
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail('Expected exception was not thrown');
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldExpectNumberOfMethodsInTimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->times(5);
        for ($i=0; $i <= 4; $i++) {
            $mock->getName();
        }
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldReturnSelfFromTimesTermInvocation()
    {
        $mock = mockme('MockMeTest_Album');
    	$object = $mock->shouldReceive('getName')->times(1);
    	$this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetReturnValueInTermsAndDefaultToReturningValueForAllCalls()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$this->assertEquals('Joe', $mock->getName());
    }

    public function testShouldSetReturnValueButThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$mock->getName();
    	try {
            $mock->mockme_verify();
            $this->fail('Expected exception was not thrown');
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldReturnValuesInOrderOfSettingButReturnLastValueRemainingOnOtherCalls()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->andReturn('Joe', 'Paddy', 'Travis');
        for ($i=0; $i <= 5; $i++) {
            $mock->getName();
        }
        $this->assertEquals('Travis', $mock->getName());
    }

    public function testShouldReturnSelfFromReturnTermInvocation()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->times(1)->andReturn('Joe');
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetExpectedArgUsingWithTerm()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('setName')->with('Black Album');
    	$mock->setName('Black Album');
    	try {
    	    $mock->mockme_verify();
    	} catch (MockMe_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldThrowExceptionIfExpectedParameterNotUsedFromWithTerm()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('setName')->with('Black Album');
    	try {
    	    $mock->setName('Black');
            $this->fail('Expected exception never materialised on unexpected parameter');
    	} catch (MockMe_Exception $e) {
    	}
    }

    // added tests

    public function testShouldSetExpectedMultipleArgsUsingWithTerm()
    {
    	$mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('setTerms')->with('album', 'music');
    	$mock->setTerms('album', 'music');
    	try {
    	    $mock->mockme_verify();
    	} catch (MockMe_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldSetExpectedMultipleArgsMatchingRegexUsingWithTerm()
    {
    	$mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('setTerms')->withArgsMatching("/\d/", "/^php/");
    	$mock->setTerms('2', 'php5');
    	try {
    	    $mock->mockme_verify();
    	} catch (MockMe_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldThrowExceptionOnArgsNotMatchingRegexUsingWithTerm()
    {
    	$mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('setTerms')->withArgsMatching("/\d/", "/^php/");
    	try {
    	    $mock->setTerms('A', 'php5');
    	    $this->fail();
    	} catch (MockMe_Exception $e) {
    	}
    }

    public function testShouldReturnSelfFromWithTermInvocation()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('setName')->times(1)->with('Joe');
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldAllowNoCallsForZeroormoretimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldAllowOneCallWithOnceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionIfLessThanOnceUsingOnceTern()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->once();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldThrowExceptionIfGreaterThanOnceUsingOnceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromOnceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->once();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldAllowTwoCallsWithTwiceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionIfLessThanTwiceUsingTwiceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldThrowExceptionIfGreaterThanTwiceUsingTwiceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromTwiceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->twice();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldAllowNoCallsWithNeverTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->never();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionIfAnyCallAfterNeverTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->never();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromNeverTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->never();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldAllowAnyCallsForZeroormoretimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        for ($i=0;$i<=4;$i++) {
            $mock->getName();
        }
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldAllowZeroCallsForZeroormoretimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldReturnSelfInstanceFromZeroormoretimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetTimesMinimumWithOnceUsingAtleastTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldSetTimesMinimumWithTimesUsingAtleastTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->times(2);
        $mock->getName();
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionIfLessThanMinimumUsingAtleastTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->twice();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAtleastTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->atLeast();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetTimesMaximumWithOnceUsingAtmostTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atMost()->once();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldSetTimesMaximumWithTimesUsingAtmostTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atMost()->times(2);
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionIfMoreThanTwiceUsingTwiceTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAtmostTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->atMost();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetTimesMinimumAndMaximumUsingLeastAndMostTerms()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldThrowExceptionWhenMinimumCallCountRangeNotMet()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldThrowExceptionWhenMaximumCallCountRangeExceeded()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        $mock->getName(); $mock->getName();
        $mock->getName(); $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail();
        } catch(MockMe_Exception $e) {
        }
    }

    public function testShouldAllowAnyArgsUsingWithanyargsTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->withAnyArgs()->andReturn('SomeName');
        $this->assertEquals('SomeName', $mock->getName('x', 'y', 'z'));
    }

    public function testShouldReturnSelfInstanceFromWithanyargsTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->withAnyArgs();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldDisallowArgsUsingWithnoargsTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->withNoArgs();
        try {
            $mock->getName('x');
            $this->fail();
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromWithnoargsTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->withNoArgs();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldObeyOrderingViaSequenceOfOrderedTermCalls()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('setName')->with('name')->ordered();
        $mock->shouldReceive('getName')->ordered();
        $mock->setName('name');
        $mock->getName();
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldDisallowMethodCallingIfMethodHasSpecifiedOrder()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->withNoArgs()->ordered();
        $mock->shouldReceive('setName')->once()->withAnyArgs()->ordered();
        try {
            $mock->setName('x');
            $this->fail();
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldAllowMethodCallingOfUnorderedExpectationsInAnyOrder()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->withNoArgs()->ordered();
        $mock->shouldReceive('setName')->once()->withAnyArgs()->ordered();
        $mock->shouldReceive('hasName')->withNoArgs(); // not ordered; call any time
        try {
            $mock->hasName();
            $mock->getName();
            $mock->setName('x');
        } catch (MockMe_Exception $e) {
            $this->fail();
        }
    }

    public function testShouldReturnSelfInstanceFromOrderedTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->ordered();
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldThrowSpecifiedExceptionUsingAndthrowTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('hasName')->andThrow('MockMeTest_Album_Exception');
        try {
            $mock->hasName();
            $this->fail();
        } catch (MockMeTest_Album_Exception $e) {
            // pass
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testShouldThrowSpecifiedExceptionWithMessageUsingAndthrowTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('hasName')->andThrow('MockMeTest_Album_Exception', 'somemessage');
        try {
            $mock->hasName();
            $this->fail();
        } catch (MockMeTest_Album_Exception $e) {
            $this->assertEquals('somemessage', $e->getMessage());
        }
    }

    public function testShouldThrowExceptionIfClassPassedToAndthrowTermNotAnException()
    {
        $mock = mockme('MockMeTest_Album');
        try {
            $mock->shouldReceive('hasName')->andThrow('MockMeTest_Album');
            $this->fail();
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAndthrowTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->andThrow('Exception');
        $this->assertTrue($object instanceof MockMe_Expectation);
    }

}
