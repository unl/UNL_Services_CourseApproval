<?php

namespace UNL\Services\CourseApproval\Search;

use UNL\Services\CourseApproval\Course\Courses;

class Results extends Courses
{
    protected $total;

    public function __construct($results, $offset = 0, $limit = -1)
    {
        $this->total = count($results);

        if ($limit > 0 && $this->total < $offset + $limit) {
            $results = array_slice($results, $offset, $limit);
        }

        parent::__construct($results);
    }

    public function count()
    {
        return $this->total;
    }
}
