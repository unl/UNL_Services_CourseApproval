<?php

namespace UNLTest\Services\CourseApproval\SubjectArea;

use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\SubjectArea\SubjectArea;
use UNL\Services\CourseApproval\XCRIService\MockService;

class SubjectAreaTest extends \PHPUnit_Framework_TestCase
{
	protected $subject;

	public function setUp()
	{
		Data::setXCRIService(new MockService());
		$this->subject = new SubjectArea('ACCT');
	}

	public function testGetSubject()
	{
		$this->assertEquals('ACCT', $this->subject->getSubject(), 'Returns correct subject code');
		$this->assertEquals('ACCT', $this->subject->__toString(), '__tostring() Returns correct subject code');
	}

	public function testGetCourses()
	{
		$courses = $this->subject->getCourses();

		$this->assertInstanceOf('ArrayAccess', $courses, 'Listings is an array');
		$this->assertEquals(2, count($courses), 'Count the number of courses.');

		$this->assertInstanceOf('UNL\Services\CourseApproval\Course\Course', $courses->current());

		$this->assertTrue(isset($courses['201']));
		$this->assertTrue(isset($courses['201H']));
		$this->assertFalse(isset($courses['foo']));
		$this->assertEquals('Introductory Accounting I', $courses['201']->title);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromBadCourseOffset()
	{
		$courses = $this->subject->getCourses();
		$course = $courses['300'];
	}

	public function testGetGroups()
	{
		$this->assertEquals(1, count($this->subject->getGroups()));
		$this->assertContains('Introductory Accounting Courses', $this->subject->getGroups());
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromAppend()
	{
		$this->subject->getCourses()->append('foo');
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromOffsetSet()
	{
		$courses = $this->subject->getCourses();
		$courses['foo'] = 'bar';
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromOffsetUnset()
	{
		$courses = $this->subject->getCourses();
		unset($courses['foo']);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromBadSubject()
	{
		$subject = new SubjectArea('foo');
		$subject->getCourses();
	}
}
