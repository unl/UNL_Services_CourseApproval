<?php

namespace UNLTest\Services\CourseApproval\Course;

use UNL\Services\CourseApproval\Course\Listing;
use UNL\Services\CourseApproval\Course\Activities;
use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\CachingService\NullService;
use UNL\Services\CourseApproval\XCRIService\MockService;

class ListingTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Data::setCachingService(new NullService());
        Data::setXCRIService(new MockService());
	}

	public function testFactory()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');

		$this->assertEquals('ACCT', $listing->subjectArea, 'Subject area');
		$this->assertEquals('201H', $listing->courseNumber, 'Course number');
		$this->assertEquals('Honors: Introductory Accounting I', $listing->title, 'Course title');
		$this->assertTrue(is_array($listing->campuses), 'Campuses');
		$this->assertTrue(is_array($listing->deliveryMethods), 'Delivery methods');
		$this->assertTrue(is_array($listing->termsOffered), 'Terms offered');
		$this->assertEquals('For course description, see ACCT 201.', $listing->description);

		$listing = Listing::createFromSubjectAndNumber('CSCE', 196);
		$this->assertInstanceOf('Countable', $listing->credits, 'Credits is a countable object.');
		$this->assertEquals(3, count($listing->credits), 'Three types of credits for this course.');

		$this->assertEquals(1, $listing->credits['Lower Range Limit'], 'Array access by type.');
		$this->assertEquals(3, $listing->credits['Upper Range Limit'], 'Array access by type 2.');
		$this->assertEquals(6, $listing->credits['Per Semester Limit'], 'Array access by type 3.');
		$this->assertFalse(isset($listing->credits['Single Value']), 'Course has no credit of this type.');
		$this->assertTrue(isset($listing->credits['Lower Range Limit']), 'Course has credit of this type.');

		$listing = Listing::createFromSubjectAndNumber('ACCT', 201);
		$this->assertEquals('Introductory Accounting I', $listing->title, 'Course title');
		$this->assertEquals('Fundamentals of accounting, reporting, and analysis to understand financial, managerial, and business concepts and practices. Provides foundation for advanced courses.', $listing->description, 'Course description');
		$this->assertEquals('Math 104 with a grade of \'C\' or better;  14 cr hrs at UNL with a 2.5 GPA.', $listing->prerequisite, 'Prerequisite');
		$this->assertEquals('ACCT 201 is \'Letter grade only\'.', $listing->notes, 'Notes');
		$this->assertEquals('letter grade only', $listing->gradingType, 'Grading type.');
		$this->assertEquals('20101', $listing->effectiveSemester, 'Effective semester');
		$this->assertInstanceOf('Countable', $listing->credits, 'Credits is a countable object.');
		$this->assertEquals(1, count($listing->credits), 'Three types of credits for this course.');
		$credits = $listing->credits;

		$this->assertNull($credits['Upper Range Limit']);

		foreach ($credits as $type => $credit) {
			// $this->assertNotNull($type);
			$this->assertNotNull($credit);
		}

		// $this->assertTrue($credits->valid());
		// $this->assertNotNull($credits->current());
		// $credits->next();
		// $this->assertFalse($credits->valid());

		$this->assertTrue(isset($listing->credits['Single Value']), 'Course has credit of this type.');
		$this->assertEquals(3, $listing->credits['Single Value'], 'Array access by type.');
	}

	public function testGetSubsequent()
	{
		$listing = Listing::createFromSubjectAndNumber('MATH', '104');

		$courses = $listing->course->getSubsequentCourses();

		$this->assertEquals(1, count($courses), 'One subsequent course returned');
		foreach ($courses as $course) {
		    $this->assertEquals('Introductory Accounting I', $course->title, 'Course title');
		    $codes = $course->getCodes();
		    $this->assertEquals('201', $codes->key());
		    $this->assertFalse(isset($codes['foo']));
		    $this->assertTrue(isset($codes['home listing']));
		}
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromListingCodesAppend()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');
		$listing->getCourse()->getCodes()->append('foo');
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromListingCodesOffsetSet()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');
		$codes = $listing->getCourse()->getCodes();
		$codes['bar'] = 'foo';
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromListingCodesOffsetUnset()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');
		$codes = $listing->getCourse()->getCodes();
		unset($codes['bar']);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromListingCreditsOffsetSet()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');
		$listing->credits['bar'] = 'foo';
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromListingCreditsOffsetUnset()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201H');
		unset($listing->credits['bar']);
	}

	public function testGetCourseActivities()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', '201');
		$this->assertInstanceOf('Iterator', $listing->activities, 'Activities returned is an iterator');
		$this->assertEquals(1, count($listing->activities), 'Count the number of activities');

		foreach ($listing->activities as $type => $act) {
			$this->assertEquals('lec', $type);
			$this->assertInstanceOf('SimpleXMLElement', $act);
		}
	}

	public function testLectureCourseActivity()
	{
		$this->assertEquals('Lecture', Activities::getFullDescription('lec'));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromBadCourseActivity()
	{
		Activities::getFullDescription('bar');
	}

	public function testDFRemovalFromListing()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', 201);
		$this->assertFalse($listing->dfRemoval, 'D or F removal');

		$listing = Listing::createFromSubjectAndNumber('ENSC', 110);
		$this->assertFalse($listing->dfRemoval, 'D or F removal');

		$listing = Listing::createFromSubjectAndNumber('CSCE', '150A');
		$this->assertTrue($listing->dfRemoval, 'D or F removal');
	}

	public function testCourseRenderGetterSetter()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', 201);
		$course = $listing->getCourse();
		$course->setRenderListing($listing);

		$this->assertEquals($listing, $course->getRenderListing());
	}

	public function testCourseRenderGetterDefault()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', 201);
		$course = $listing->getCourse();

		$this->assertEquals($listing, $course->getRenderListing());
	}

	public function testCourseProperties()
	{
		$listing = Listing::createFromSubjectAndNumber('ACCT', 201);
		$this->assertTrue(isset($listing->notes), 'Course has notes.');
		$this->assertTrue(isset($listing->description), 'Course has description.');
		$this->assertFalse(isset($listing->aceOutcomes), 'Course does NOT have ACE outcomes.');
	}
}
