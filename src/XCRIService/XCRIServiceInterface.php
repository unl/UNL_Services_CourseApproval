<?php

namespace UNL\Services\CourseApproval\XCRIService;

interface XCRIServiceInterface
{
    public function getAllCourses();
    public function getSubjectArea($subjectarea);
}
