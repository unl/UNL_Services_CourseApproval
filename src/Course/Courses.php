<?php

namespace UNL\Services\CourseApproval\Course;

class Courses extends \ArrayIterator
{
    public function __construct($courses)
    {
        parent::__construct($courses);
    }

    /**
     * Get the current course
     *
     * @return Course
     */
    public function current()
    {
        return new Course(parent::current());
    }
}
