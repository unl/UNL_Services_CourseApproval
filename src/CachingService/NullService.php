<?php

namespace UNL\Services\CourseApproval\CachingService;

class NullService implements CachingServiceInterface
{
    public function get($key)
    {
        // Expired cache always.
        return false;
    }

    public function save($key, $data)
    {
        // Make it appear as though it was saved.
        return true;
    }
}
