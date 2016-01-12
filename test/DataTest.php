<?php

namespace UNLTest\Services\CourseApproval;

use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\CachingService\NullService;
use UNL\Services\CourseApproval\CachingService\CacheLite;

class DataTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		try {
			$cache = new CacheLite();
		} catch (\Exception $exception) {
			// this is expected
		}

		if (!class_exists('Cache_Lite', false)) {
			class_alias('UNLTest\Services\CourseApproval\MockCacheLite', 'Cache_Lite');
		}

		$this->tearDown();
	}

	public function tearDown()
	{
		Data::setCachingService();
		Data::setXCRIService();
	}

	public function testMockCacheLite()
	{
		$this->assertInstanceOf('UNL\Services\CourseApproval\CachingService\CacheLite', Data::getCachingService());
	}

	public function testDefaultXCRIService()
	{
		$this->assertInstanceOf('UNL\Services\CourseApproval\XCRIService\Creq', Data::getXCRIService());
		Data::getXCRIService()->setUrl();
		$courses = Data::getXCRIService()->getAllCourses();
		$this->assertTrue(!empty($courses));
		$courses = Data::getXCRIService()->getSubjectArea('ACCT');
		$this->assertTrue(!empty($courses));

		Data::setXCRIService();
		Data::setCachingService(new NullService());
		$courses = Data::getXCRIService()->getSubjectArea('ACCT');
		$this->assertTrue(!empty($courses));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testExpectExceptionFromBadCreqUrl()
	{
		Data::getXCRIService()->setUrl('http://nowhere.unl.edu/404');
		Data::getXCRIService()->getSubjectArea('foo');
	}
}
