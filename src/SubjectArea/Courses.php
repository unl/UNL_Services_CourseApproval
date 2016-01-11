<?php

namespace UNL\Services\CourseApproval\SubjectArea;

use UNL\Services\CourseApproval\Course\Course;

class Courses extends \ArrayIterator
{
    const DEFAULT_XPATH = '//default:courses/default:course';

    protected $subjectArea;

    protected $offsetLookupCache = array();

    public function __construct(SubjectArea $subjectArea)
    {
        $this->subjectArea = $subjectArea;
        parent::__construct($subjectArea->getCourseXmlObject()->xpath(static::DEFAULT_XPATH));
    }

    protected function createCourse(\SimpleXMLElement $xml)
    {
        $course = new Course($xml);
        $course->setSubject($this->subjectArea->getSubject());
        return $course;
    }

    public function current()
    {
        return $this->createCourse(parent::current());
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function append($value)
    {
        throw new \Exception('This is a readonly collection.');
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($number, $value)
    {
        throw new \Exception('This is a readonly collection.');
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($number)
    {
        throw new \Exception('This is a readonly collection.');
    }

    /**
     * @param  string $number
     * @return \SimpleXMLElement[]
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function offsetLookup($number)
    {
        if (!isset($this->offsetLookupCache[$number])) {
            $parts = array();
            if (!Course::validCourseNumber($number, $parts)) {
                throw new \Exception('Invalid course number format '.$number);
            }

            $letterCheck = 'not(' . Course::DEFAULT_NS . ':courseLetter)';
            if (!empty($parts['courseLetter'])) {
                $letterCheck = sprintf('%s:courseLetter="%s"', Course::DEFAULT_NS, $parts['courseLetter']);
            }

            $xpath = sprintf(
                '%1$s/%2$s:courseCodes/%2$s:courseCode[%2$s:subject="%3$s" and %2$s:courseNumber="%4$s" and %5$s]/parent::*/parent::*',
                static::DEFAULT_XPATH,
                Course::DEFAULT_NS,
                $this->subjectArea->getSubject(),
                $parts['courseNumber'],
                $letterCheck
            );

            $courses = $this->subjectArea->getCourseXmlObject()->xpath($xpath);
            $this->offsetLookupCache[$number] = $courses;
        }

        return $this->offsetLookupCache[$number];
    }

    public function offsetExists($number)
    {
        try {
            $lookup = $this->offsetLookup($number);
        } catch (\Exception $e) {
            $lookup = null;
        }

        return !empty($lookup);
    }

    public function offsetGet($number)
    {
        $courses = $this->offsetLookup($number);

        if (empty($courses)) {
            throw new \Exception('No course was found matching '.$this->subjectArea->getSubject().' '.$number, 404);
        }

        return $this->createCourse(current($courses));
    }
}
