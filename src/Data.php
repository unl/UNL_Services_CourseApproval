<?php

namespace UNL\Services\CourseApproval;

abstract class Data
{
    /**
     * The caching service used.
     *
     * @var CachingService\CachingServiceInterface
     */
    protected static $cache;

    /**
     * The XCRI service used.
     *
     * @var XCRIService\XCRIServiceInterface
     */
    protected static $xcri;

    /**
     * Get the static caching service
     *
     * @return CachingService\CachingServiceInterface
     */
    public static function getCachingService()
    {
        if (!isset(static::$cache)) {
            $serviceClass = 'NullService';

            if (class_exists('Cache_Lite')) {
                $serviceClass = 'CacheLite';
            }

            $serviceClass = __NAMESPACE__ . '\\' . 'CachingService\\' . $serviceClass;
            static::setCachingService(new $serviceClass());
        }

        return static::$cache;
    }

    /**
     * Set the static caching service
     *
     * @param CachingService\CachingServiceInterface $service The caching service to use
     * @return CachingService\CachingServiceInterface
     */
    public static function setCachingService(CachingService\CachingServiceInterface $service = null)
    {
        static::$cache = $service;
        return static::$cache;
    }

    /**
     * Gets the XCRI service we're subscribed to.
     *
     * @return XCRIService\XCRIServiceInterface
     */
    public static function getXCRIService()
    {
        if (!isset(static::$xcri)) {
            static::setXCRIService(new XCRIService_Creq());
        }

        return static::$xcri;
    }

    /**
     * Set the static XCRI service
     *
     * @param XCRIService\XCRIServiceInterface $xcri The XCRI service object
     * @return XCRIService\XCRIServiceInterface
     */
    public static function setXCRIService(XCRIService\XCRIServiceInterface $xcri = null)
    {
        self::$xcri = $xcri;
        return self::$xcri;
    }
}
