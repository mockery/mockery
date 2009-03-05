<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/Mockery/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockeryExpectationsTest extends PHPUnit_Framework_TestCase
{

    public function testShouldReturnTrueOnValidationByDefaultIfNoExpectationsSet()
    {
        $mock = mockery('MockeryTest_EmptyClass');
        try {
            $this->assertTrue($mock->mockery_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }

    public function testShouldThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName');
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail('Expected exception was not thrown');
        } catch (Mockery_Exception $e) {
        }
    }

    public function testShouldExpectNumberOfMethodsInTimesTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->times(5);
        for ($i=0; $i <= 4; $i++) {
            $mock->getName();
        }
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldReturnSelfFromTimesTermInvocation()
    {
        $mock = mockery('MockeryTest_Album');
    	$object = $mock->shouldReceive('getName')->times(1);
    	$this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldSetReturnValueInTermsAndDefaultToReturningValueForAllCalls()
    {
        $mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$this->assertEquals('Joe', $mock->getName());
    }

    public function testShouldSetReturnValueButThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$mock->getName();
    	try {
            $mock->mockery_verify();
            $this->fail('Expected exception was not thrown');
        } catch (Mockery_Exception $e) {
        }
    }

    public function testShouldReturnValuesInOrderOfSettingButReturnLastValueRemainingOnOtherCalls()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->andReturn('Joe', 'Paddy', 'Travis');
        for ($i=0; $i <= 5; $i++) {
            $mock->getName();
        }
        $this->assertEquals('Travis', $mock->getName());
    }

    public function testShouldReturnSelfFromReturnTermInvocation()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->times(1)->andReturn('Joe');
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldSetExpectedArgUsingWithTerm()
    {
        $mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('setName')->with('Black Album');
    	$mock->setName('Black Album');
    	try {
    	    $mock->mockery_verify();
    	} catch (Mockery_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldThrowExceptionIfExpectedParameterNotUsedFromWithTerm()
    {
        $mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('setName')->with('Black Album');
    	try {
    	    $mock->setName('Black');
            $this->fail('Expected exception never materialised on unexpected parameter');
    	} catch (Mockery_Exception $e) {
    	}
    }

    // added tests

    public function testShouldSetExpectedMultipleArgsUsingWithTerm()
    {
    	$mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('setTerms')->with('album', 'music');
    	$mock->setTerms('album', 'music');
    	try {
    	    $mock->mockery_verify();
    	} catch (Mockery_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldSetExpectedMultipleArgsMatchingRegexUsingWithTerm()
    {
    	$mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('setTerms')->withArgsMatching("/\d/", "/^php/");
    	$mock->setTerms('2', 'php5');
    	try {
    	    $mock->mockery_verify();
    	} catch (Mockery_Exception $e) {
    	    $this->fail($e->getMessage());
    	}
    }

    public function testShouldThrowExceptionOnArgsNotMatchingRegexUsingWithTerm()
    {
    	$mock = mockery('MockeryTest_Album');
    	$mock->shouldReceive('setTerms')->withArgsMatching("/\d/", "/^php/");
    	try {
    	    $mock->setTerms('A', 'php5');
    	    $this->fail();
    	} catch (Mockery_Exception $e) {
    	}
    }

    public function testShouldReturnSelfFromWithTermInvocation()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('setName')->times(1)->with('Joe');
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldAllowNoCallsForZeroormoretimesTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldAllowOneCallWithOnceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionIfLessThanOnceUsingOnceTern()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->once();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldThrowExceptionIfGreaterThanOnceUsingOnceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromOnceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->once();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldAllowTwoCallsWithTwiceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionIfLessThanTwiceUsingTwiceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldThrowExceptionIfGreaterThanTwiceUsingTwiceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->once();
        $mock->getName();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromTwiceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->twice();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldAllowNoCallsWithNeverTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->never();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionIfAnyCallAfterNeverTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->never();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromNeverTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->never();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldAllowAnyCallsForZeroormoretimesTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        for ($i=0;$i<=4;$i++) {
            $mock->getName();
        }
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldAllowZeroCallsForZeroormoretimesTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldReturnSelfInstanceFromZeroormoretimesTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->zeroOrMoreTimes();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldSetTimesMinimumWithOnceUsingAtleastTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldSetTimesMinimumWithTimesUsingAtleastTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->times(2);
        $mock->getName();
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionIfLessThanMinimumUsingAtleastTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->twice();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAtleastTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->atLeast();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldSetTimesMaximumWithOnceUsingAtmostTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atMost()->once();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldSetTimesMaximumWithTimesUsingAtmostTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atMost()->times(2);
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionIfMoreThanTwiceUsingTwiceTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->twice();
        $mock->getName();
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAtmostTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->atMost();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldSetTimesMinimumAndMaximumUsingLeastAndMostTerms()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        $mock->getName();
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldThrowExceptionWhenMinimumCallCountRangeNotMet()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldThrowExceptionWhenMaximumCallCountRangeExceeded()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->atLeast()->once()->atMost()->times(3);
        $mock->getName(); $mock->getName();
        $mock->getName(); $mock->getName();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

    public function testShouldAllowAnyArgsUsingWithanyargsTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->withAnyArgs()->andReturn('SomeName');
        $this->assertEquals('SomeName', $mock->getName('x', 'y', 'z'));
    }

    public function testShouldReturnSelfInstanceFromWithanyargsTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->withAnyArgs();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldDisallowArgsUsingWithnoargsTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->withNoArgs();
        try {
            $mock->getName('x');
            $this->fail();
        } catch (Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromWithnoargsTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->withNoArgs();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldObeyOrderingViaSequenceOfOrderedTermCalls()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('setName')->with('name')->ordered();
        $mock->shouldReceive('getName')->ordered();
        $mock->setName('name');
        $mock->getName();
        $this->assertTrue($mock->mockery_verify());
    }

    public function testShouldDisallowMethodCallingIfMethodHasSpecifiedOrder()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->withNoArgs()->ordered();
        $mock->shouldReceive('setName')->once()->withAnyArgs()->ordered();
        try {
            $mock->setName('x');
            $this->fail();
        } catch (Mockery_Exception $e) {
        }
    }

    public function testShouldAllowMethodCallingOfUnorderedExpectationsInAnyOrder()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('getName')->withNoArgs()->ordered();
        $mock->shouldReceive('setName')->once()->withAnyArgs()->ordered();
        $mock->shouldReceive('hasName')->withNoArgs(); // not ordered; call any time
        try {
            $mock->hasName();
            $mock->getName();
            $mock->setName('x');
        } catch (Mockery_Exception $e) {
            $this->fail();
        }
    }

    public function testShouldReturnSelfInstanceFromOrderedTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->ordered();
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

    public function testShouldThrowSpecifiedExceptionUsingAndthrowTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('hasName')->andThrow('MockeryTest_Album_Exception');
        try {
            $mock->hasName();
            $this->fail();
        } catch (MockeryTest_Album_Exception $e) {
            // pass
        } catch (Exception $e) {
            $this->fail();
        }
    }

    public function testShouldThrowSpecifiedExceptionWithMessageUsingAndthrowTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $mock->shouldReceive('hasName')->andThrow('MockeryTest_Album_Exception', 'somemessage');
        try {
            $mock->hasName();
            $this->fail();
        } catch (MockeryTest_Album_Exception $e) {
            $this->assertEquals('somemessage', $e->getMessage());
        }
    }

    public function testShouldThrowExceptionIfClassPassedToAndthrowTermNotAnException()
    {
        $mock = mockery('MockeryTest_Album');
        try {
            $mock->shouldReceive('hasName')->andThrow('MockeryTest_Album');
            $this->fail();
        } catch (Mockery_Exception $e) {
        }
    }

    public function testShouldReturnSelfInstanceFromAndthrowTerm()
    {
        $mock = mockery('MockeryTest_Album');
        $object = $mock->shouldReceive('getName')->andThrow('Exception');
        $this->assertTrue($object instanceof Mockery_Expectation);
    }

}
