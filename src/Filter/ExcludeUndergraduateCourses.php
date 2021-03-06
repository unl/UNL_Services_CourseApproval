<?php

namespace UNL\Services\CourseApproval\Filter;

class ExcludeUndergraduateCourses extends \FilterIterator
{
    public function accept()
    {
        $course = $this->getInnerIterator()->current();

        foreach ($course->getCodes() as $listing) {
            if ($listing->getCourseNumber() >= 500) {
                return true;
            }
        }

        return false;
    }
}
