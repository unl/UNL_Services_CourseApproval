<?php

namespace UNL\Services\CourseApproval\CachingService;

class CacheLite implements CachingServiceInterface
{
    const CACHE_NAMESPACE = 'ugbulletin';

    protected $cache;

    public function __construct()
    {
        if (!class_exists('Cache_Lite')) {
            throw new \Exception('Unable to include Cache_Lite, is it installed?');
        }

        $options = array('lifeTime' => 604800); //one week lifetime
        $this->cache = new \Cache_Lite($options);
    }

    public function save($key, $data)
    {
        return $this->cache->save($data, $key, static::CACHE_NAMESPACE);
    }

    public function get($key)
    {
        if ($data = $this->cache->get($key, static::CACHE_NAMESPACE)) {
            return $data;
        }
        return false;
    }
}
