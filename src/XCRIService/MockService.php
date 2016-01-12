<?php

namespace UNL\Services\CourseApproval\XCRIService;

class MockService implements XCRIServiceInterface
{
    const XML_HEADER = '<?xml version="1.0" encoding="UTF-8"?>
<courses xmlns="http://courseapproval.unl.edu/courses" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://courseapproval.unl.edu/courses /schema/courses.xsd">';

    const XML_FOOTER = '</courses>';

    protected $mockData = array();

    public function __construct()
    {
        $this->mockData = include __DIR__ . '/../../test/data/mock-xcriservice.php';
    }

    public function getAllCourses()
    {
        return static::XML_HEADER . implode($this->mockData) . static::XML_FOOTER;
    }

    public function getSubjectArea($subjectarea)
    {
        if (!isset($this->mockData[$subjectarea])) {
            throw new \Exception('Could not get data.', 500);
        }

        return static::XML_HEADER . $this->mockData[$subjectarea] . static::XML_FOOTER;
    }
}
