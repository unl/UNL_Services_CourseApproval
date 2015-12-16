<?php

namespace UNL\Services\CourseApproval\CachingService;

interface CachingServiceInterface
{
    public function save($key, $data);
    public function get($key);
}
