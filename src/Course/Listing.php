<?php

namespace UNL\Services\CourseApproval\Course;

use UNL\Services\CourseApproval\SubjectArea\SubjectArea;
use UNL\Services\CourseApproval\Search\Search;

/**
 * A representation of a course code
 *
 * @property Course $course The course
 */
class Listing
{
    /**
     * The subject area for this listing eg ACCT
     *
     * @var string
     */
    protected $subjectArea;

    /**
     * The course number eg 201
     *
     * @var string
     */
    protected $courseNumber;

    protected $type;

    protected $groups = array();

    protected $courseCodeXml;

    protected static function getCourseNumberFromCourseCodeXml(\SimpleXMLElement $xml)
    {
        $number = (string) $xml->courseNumber;
        if (isset($xml->courseLetter)) {
            $number .= (string) $xml->courseLetter;
        }
        return $number;
    }

    protected static function getListingGroupsFromCourseCodeXml(\SimpleXMLElement $xml)
    {
        $groups = array();
        if (isset($xml->courseGroup)) {
            foreach ($xml->courseGroup as $group) {
                $groups[] = (string) $group;
            }
        }
        return $groups;
    }

    /**
     * @param string $subject
     * @param string $number
     * @return self
     */
    public static function createFromSubjectAndNumber($subject, $number)
    {
        $number = (string) $number;
        $subjectArea = new SubjectArea($subject);

        /* @var $course Course */
        $course = $subjectArea->courses[$number];

        /* @var $candidateListing self */
        foreach ($course->codes as $candidateListing) {
            if ($candidateListing->subjectArea === $subject && $candidateListing->courseNumber === $number) {
                return $candidateListing;
            }
        }

        return null;
    }

    /**
     * @param Course $course
     * @param \SimpleXMLElement $courseCodeXml
     */
    public function __construct(Course $course, \SimpleXMLElement $courseCodeXml)
    {
        $this->_course = $course;
        $this->courseCodeXml = $courseCodeXml;
        $this->subjectArea = (string) $courseCodeXml->subject;
        $this->courseNumber = static::getCourseNumberFromCourseCodeXml($courseCodeXml);
        $this->groups = static::getListingGroupsFromCourseCodeXml($courseCodeXml);
        $this->type = (string) $courseCodeXml['type'];
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getSubject()
    {
        return $this->subjectArea;
    }

    public function getCourseNumber()
    {
        return $this->courseNumber;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Listing[]
     */
    public function getListingsFromSubject()
    {
        $listings = array();

        foreach ($this->course->getCodes() as $listing) {
            if ($this->subjectArea !== $listing->getSubject()) {
                continue;
            }

            $listings[] = $listing;
        }

        return $listings;
    }

    /**
     * @return Listing[]
     */
    public function getCrosslistingsBySubject()
    {
        $listings = array();

        foreach ($this->course->getCodes() as $listing) {
            if ($this->subjectArea === $listing->getSubject()) {
                continue;
            }

            if (!isset($listings[$listing->subjectArea])) {
                $listings[$listing->subjectArea] = array();
            }

            $listings[$listing->subjectArea][] = $listing;
        }

        return $listings;
    }

    public function isCrosslisting()
    {
        return $this->type === Course::COURSE_CODE_TYPE_CROSS;
    }

    /**
     * Search for subsequent courses based on listing type
     * (reverse prereqs)
     *
     * @param Search $searchDriver
     * @return Courses
     */
    public function getSubsequentCourses($searchDriver = null)
    {
        if (!$this->isCrosslisting()) {
            return $this->_course->getSubsequentCourses($searchDriver);
        }

        $searcher = new Search($searchDriver);
        $query = $this->subjectArea . ' ' . $this->courseNumber;
        return $searcher->byPrerequisite($query);
    }

    public function hasGroups()
    {
        return !empty($this->groups);
    }
}
