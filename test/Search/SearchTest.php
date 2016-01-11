<?php

namespace UNLTest\Services\CourseApproval\Search;

use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\CachingService\NullService;
use UNL\Services\CourseApproval\Search\Search;
use UNL\Services\CourseApproval\XCRIService\MockService;

class SearchTest extends \PHPUnit_Framework_TestCase
{
    protected $search;

    public function setUp()
    {
        Data::setCachingService(new NullService());
        Data::setXCRIService(new MockService());
        $this->search = new Search();
    }

    public function testByNumber()
    {
        $courses = $this->search->byNumber('201');
        $this->assertEquals(2, count($courses), 'Two results returned');
    }

    public function testNumberSuffixQuery()
    {
        $courses = $this->search->numberSuffixQuery('04');
        $this->assertEquals(1, count($courses), 'One *04 result returned');
    }

    public function testByTitle()
    {
        $courses = $this->search->byTitle('Accounting');
        $this->assertEquals(2, count($courses), 'Two results returned');

        foreach ($courses as $course) {
            $this->assertNotFalse(
                strpos($course->title, 'Accounting'),
                'Course title contains the word Accounting'
            );
        }
    }

    /**
     * @dataProvider subjectAreaProvider
     */
    public function testBySubject($subject, $expectedCount)
    {
        $courses = $this->search->bySubject($subject);
        $this->assertEquals($expectedCount, count($courses));
    }

    public function testInersectQuery()
    {
        $query1 = $this->search->subjectAreaQuery('NREE');
        $query2 = $this->search->subjectAreaQuery('AECN');
        $query = $this->search->intersectQuery($query1, $query2);
        $courses = $this->search->getQueryResult($query);
        $this->assertEquals(1, count($courses), 'Intersection of two queries');

        $query1 = $this->search->aceQuery('10');
        $query2 = $this->search->subjectAreaQuery('AECN');
        $query = $this->search->intersectQuery($query1, $query2);
        $courses = $this->search->getQueryResult($query);
        $this->assertEquals(2, count($courses), 'Intersection of AECN and ACE 10 queries');
    }

    public function subjectAreaProvider()
    {
        return array(
            array('NREE', 2),
            array('AECN', 2),
        );
    }

    public function testByPrerequisite()
    {
        $courses = $this->search->byPrerequisite('ECON 211');
        $this->assertEquals(2, count($courses));
    }

    public function testByAnyQueries()
    {
        $courses = $this->search->byAny('4 credits');
        $this->assertEquals(0, count($courses));

        $courses = $this->search->byAny('ACE 3');
        $this->assertEquals(1, count($courses), 'One ACE 3 result returned');

        $courses = $this->search->byAny('ACE 10');
        $this->assertEquals(3, count($courses), 'Three ACE 10 results returned');

        $courses = $this->search->byAny('ACE');
        $this->assertEquals(4, count($courses), 'Four ACE results returned');

        $courses = $this->search->byAny('ACE 10 AECN');
        $this->assertEquals(2, count($courses), 'byAny for AECN and ACE 10');

        $courses = $this->search->byAny('ACE 4XX');
        $this->assertEquals(3, count($courses), 'byAny for 4XX and ACE 10');

        $courses = $this->search->byAny('AECN ACE 10');
        $this->assertEquals(2, count($courses), 'byAny for ACE 10 and AECN');

        $courses = $this->search->byAny('MATH ACE');
        $this->assertEquals(1, count($courses), 'One MATH ACE course');

        $courses = $this->search->byAny('AECN');
        $this->assertEquals(2, count($courses), 'Two AECN results returned');

        $courses = $this->search->byAny('MATH');
        $this->assertEquals(2, count($courses), 'Two MATH courses');

        $courses = $this->search->byAny('MATH *41');
        $this->assertEquals(1, count($courses));

        $courses = $this->search->byAny('honors');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Search for "honors", should return 1 course');

        $courses = $this->search->byAny('201H');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Search for "201H" returns 1 course');

        $courses = $this->search->byAny('201h');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Search for "201h" (lowercase h) returns 1 course');

        $courses = $this->search->byAny('ACCT 201H');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Search for "ACCT 201H" returns 1 course');

        $courses = $this->search->byAny('ACCT 201h');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Search for "ACCT 201h" returns 1 course');

        $courses = $this->search->byAny('2XX');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of 2XX results');

        $courses = $this->search->byAny('2xx');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of 2xx results');

        $courses = $this->search->byAny('1XX');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(4, count($courses), 'Count the number of 1XX results');

        $courses = $this->search->byAny('3xx');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(0, count($courses), 'Count the number of 3xx results');

        $courses = $this->search->byAny('ACCT 2xx');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of ACCT 2xx results');

        $courses = $this->search->byAny('ACCT 2XX');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of ACCT 2XX results');

        $courses = $this->search->byAny('201');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('425');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('300');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(0, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('ACCT 201');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('acct 201');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(2, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('AECN 425', 0, 2);
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(1, count($courses), 'Count the number of results');

        $courses = $this->search->byAny('LLLL 101');
        $this->assertInstanceOf('UNL\Services\CourseApproval\Search\Results', $courses, 'Search returns a result object');
        $this->assertEquals(0, count($courses), 'Count the number of results');
    }

    public function testByManyQueries()
    {
        $courses = $this->search->byMany(array('ACE 10', 'AECN'));
        $this->assertEquals(2, count($courses), 'byMany for AECN and ACE 10');

        $courses = $this->search->byMany(array('AECN', 'ACE 10'));
        $this->assertEquals(2, count($courses), 'byMany for ACE 10 and AECN');

        $courses = $this->search->byMany(array('MATH', 'ACE'));
        $this->assertEquals(1, count($courses), 'One MATH ACE course');
    }

    public function testGraduateCourses()
    {
        $courses = $this->search->graduateCourses();
        $this->assertEquals(2, count($courses), 'Two graduate courses returned');
    }

    public function testUnderraduateCourses()
    {
        $courses = $this->search->undergraduateCourses();
        $this->assertEquals(10, count($courses));
    }

    public function testBadQuery()
    {
        $courses = $this->search->getQueryResult('string(a/@b)');
        $this->assertEquals(0, count($courses));
    }

    public function testWithExcludeGradFilter()
    {
        $courses = $this->search->byAny('AECN');
        $courses = new \UNL\Services\CourseApproval\Filter\ExcludeGraduateCourses($courses);

        $this->assertEquals(2,  iterator_count($courses));

        foreach ($courses as $course) {
            $this->assertNull($course->getSubject());
        }
    }

    public function testWithExcludeUndergraduateFilter()
    {
        $courses = $this->search->byAny('AECN');
        $courses = new \UNL\Services\CourseApproval\Filter\ExcludeUndergraduateCourses($courses);

        $this->assertEquals(0,  iterator_count($courses));

        foreach ($courses as $course) {
            $this->assertNull($course->getSubject());
        }
    }
}
