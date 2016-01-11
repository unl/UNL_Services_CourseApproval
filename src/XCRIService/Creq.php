<?php

namespace UNL\Services\CourseApproval\XCRIService;

use UNL\Services\CourseApproval\Data;

class Creq implements XCRIServiceInterface
{

    /**
     * URL to the public creq XML data service endpoint
     *
     * @var string
     */
    const URL = 'http://creq.unl.edu/courses/public-view/all-courses';

    /**
     * The caching service.
     *
     * @var UNL_Services_CourseApproval_CachingService
     */
    protected $cache;

    /**
     * Constructor for the creq service
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct()
    {
        $this->cache = Data::getCachingService();
    }

    /**
     * Get all course data
     *
     * @return string XML course data
     */
    public function getAllCourses()
    {
        return $this->getData('creq_allcourses', static::URL);
    }

    /**
     * Get the XML for a specific subject area, e.g. CSCE
     *
     * @param string $subjectarea Subject area/code to retrieve courses for e.g. CSCE
     *
     * @return string XML data
     */
    public function getSubjectArea($subjectarea)
    {
        return $this->getData('creq_subject_'.$subjectarea, static::URL.'/subject/'.$subjectarea);
    }

    /**
     * Generic data retrieval method which grabs a URL and caches the data
     *
     * @param string $key A unique key for this piece of data
     * @param string $url The URL to retrieve data from
     *
     * @return string The data from the URL
     *
     * @throws Exception
     */
    protected function getData($key, $url)
    {
        if ($data = $this->cache->get($key)) {
            return $data;
        }

        if ($data = file_get_contents($url)) {
            if ($this->cache->save($key, $data)) {
                return $data;
            }
            throw new Exception('Could not save data for '.$url);
        }

        throw new Exception('Could not get data from '.$url);
    }
}