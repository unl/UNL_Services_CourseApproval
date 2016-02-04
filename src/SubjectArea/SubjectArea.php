<?php

namespace UNL\Services\CourseApproval\SubjectArea;

use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\Course\Course;

class SubjectArea
{
    protected $subject;

    /**
     * Collection of courses
     *
     * @var Courses
     */
    protected $courses;

    /**
     * array of groups if any
     * @var Groups
     */
    protected $groups;

    /**
     * @var \SimpleXmlElement
     */
    protected $xml;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'subject':
                return $this->getSubject();
            case 'courses':
                return $this->getCourses();
            case 'groups':
                return $this->getGroups();
            default:
                return null;
        }
    }

    /**
     * @return \SimpleXMLElement
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getCourseXmlObject()
    {
        if (!isset($this->xml)) {
            $this->xml = new \SimpleXMLElement(Data::getXCRIService()->getSubjectArea($this->subject));
            Course::registerXPathNamespaces($this->xml);
        }

        return $this->xml;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getCourses()
    {
        if (!$this->courses) {
            $this->courses = new Courses($this);
        }

        return $this->courses;
    }

    public function getGroups()
    {
        if (!$this->groups) {
            $this->groups = new Groups($this);
        }

        return $this->groups;
    }

    public function __toString()
    {
        return $this->getSubject();
    }
}
