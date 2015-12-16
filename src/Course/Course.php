<?php

namespace UNL\Services\CourseApproval\Course;

use UNL\Services\CourseApproval\Search\Search;

class Course
{
    const DEFAULT_NS = 'default';
    const DEFAULT_NS_PREFIX = 'default:';

    const COURSE_CODE_TYPE_HOME = 'home listing';
    const COURSE_CODE_TYPE_CROSS = 'crosslisting';
    const COURSE_CODE_TYPE_GRAD = 'grad tie-in';

    /**
     * The subject area the course was loaded for/from
     *
     * @var string $subject
     */
    protected $subject;

    /**
     * The listing to use to render listing specific information
     *
     * @var Listing $renderListing
     */
    protected $renderListing;

    /**
     * The internal object
     *
     * @var \SimpleXMLElement
     */
    protected $internal;

    /**
     * Collection of course codes
     *
     * @var Codes
     */
    protected $codes;

    protected $getMap = array(
        'credits' => 'getCredits',
        'dfRemoval' => 'hasDFRemoval',
        'campuses' => 'getCampuses',
        'deliveryMethods' => 'getDeliveryMethods',
        'termsOffered' => 'getTermsOffered',
        'activities' => 'getActivities',
        'aceOutcomes' => 'getACEOutcomes',
    );

    protected $nsPrefix = '';

    public static function registerXPathNamespaces(\SimpleXMLElement $xml, $defaultNamespace = self::DEFAULT_NS)
    {
        $namespaces = $xml->getNamespaces(true);
        if (isset($namespaces['']) && $namespaces[''] == 'http://courseapproval.unl.edu/courses') {
            $xml->registerXPathNamespace($defaultNamespace, $namespaces['']);

            //Register the rest with their prefixes
            foreach ($namespaces as $prefix => $namespace) {
                $xml->registerXPathNamespace($prefix, $namespace);
            }
        }
    }

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->internal = $xml;
        $this->nsPrefix = static::DEFAULT_NS_PREFIX;
        static::registerXPathNamespaces($this->internal);
        $this->codes = new Codes($this);
    }

    public function __get($var)
    {
        if (array_key_exists($var, $this->getMap)) {
            return $this->{$this->getMap[$var]}();
        }

        if (isset($this->internal->$var)  && count($this->internal->$var->children())) {
            if (isset($this->internal->$var->div)) {
                return strip_tags(html_entity_decode($this->internal->$var->div->asXML()));
            }
        }

        return (string) $this->internal->$var;
    }

    public function __isset($var)
    {
        $elements = $this->internal->xpath($this->nsPrefix . $var);
        if (count($elements)) {
            return true;
        }
        return false;
    }

    public function getXmlObject()
    {
        return $this->internal;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getCodes()
    {
        return $this->codes;
    }

    public function getCampuses()
    {
        return $this->getArray('campuses');
    }

    public function getTermsOffered()
    {
        return $this->getArray('termsOffered');
    }

    public function getDeliveryMethods()
    {
        return $this->getArray('deliveryMethods');
    }

    public function getActivities()
    {
        return new Activities($this->internal->activities->children());
    }

    public function getACEOutcomes()
    {
        return $this->getArray('aceOutcomes');
    }

    public function getArray($var)
    {
        $results = array();

        if (isset($this->internal->$var)) {
            foreach ($this->internal->$var->children() as $el) {
                $results[] = (string)$el;
            }
        }

        return $results;
    }

    /**
     * Gets the types of credits offered for this course.
     *
     * @return UNL_Services_CourseApproval_Course_Credits
     */
    public function getCredits()
    {
        if (!$this->internal->credits) {
            return array();
        }
        return new Credits($this->internal->credits->children());
    }

    /**
     * Checks whether this course can remove a previous grade of D or F for the same course.
     *
     * @return bool
     */
    public function hasDFRemoval()
    {
        if ($this->internal->dfRemoval == 'true') {
            return true;
        }

        return false;
    }

    public function setRenderListing(Listing $listing)
    {
        $this->renderListing = $listing;
    }

    public function getRenderListing()
    {
        if ($this->renderListing) {
            return $this->renderListing;
        }

        if (!isset($this->subject)) {
            return $this->getHomeListing();
        }

        foreach ($this->codes as $listing) {
            if ($listing->getSubject() === $this->subject) {
                return $listing;
            }
        }

        return null;
    }

    /**
     * Verifies that the course number is in the correct format.
     *
     * @param $number The course number eg 201H, 4004I
     * @param $parts  Array of matched parts
     *
     * @return bool
     */
    public static function validCourseNumber($number, &$parts = null)
    {
        $matches = array();
        if (preg_match('/^([\d]?[\d]{2,3})([A-Z])?$/i', $number, $matches)) {
            $parts['courseNumber'] = $matches[1];
            if (isset($matches[2])) {
                $parts['courseLetter'] = $matches[2];
            }
            return true;
        }

        return false;
    }

    protected function getCourseCodeByType($type)
    {
        return $this->codes[$type];
    }

    public function getHomeListing()
    {
        return $this->getListingByType(static::COURSE_CODE_TYPE_HOME);
    }

    /**
     * Returns the first listing object that represents the interal courseCode with the given type
     *
     * @param string $type
     * @return Listing
     */
    public function getListingByType($type)
    {
        $courseCode = $this->getCourseCodeByType($type);

        if (empty($courseCode)) {
            return null;
        }

        return current($courseCode);
    }

    /**
     * Returns all of the listing objects that match the given type
     *
     * @param string $type
     * @return Listing[]
     */
    public function getListingsByType($type)
    {
        return $this->getCourseCodeByType($type);
    }

    /**
     * Search for subsequent courses
     *
     * (reverse prereqs)
     *
     * @param Search $searchDriver
     * @return Courses
     */
    public function getSubsequentCourses($searchDriver = null)
    {
        $searcher = new Search($searchDriver);
        $homeListing = $this->getHomeListing();

        $query = $homeListing->getSubject() . ' ' . $homeListing->getCourseNumber();
        return $searcher->byPrerequisite($query);
    }

    public function asXML()
    {
        return $this->internal->asXML();
    }

    public static function getPossibleAceOutcomes()
    {
        //Value=>Description
        return array(
            1 => 'ACE 1',
            2 => 'ACE 2',
            3 => 'ACE 3',
            4 => 'ACE 4',
            5 => 'ACE 5',
            6 => 'ACE 6',
            7 => 'ACE 7',
            8 => 'ACE 8',
            9 => 'ACE 9',
            10 => 'ACE 10',
        );
    }

    public static function getPossibleCampuses()
    {
        //Value=>Description
        return array(
            'UNL' => 'University of Nebraska Lincoln',
            'UNO' => 'University of Nebraska Omaha',
            'UNMC' => 'University of Nebraska Medical University',
            'UNK' => 'University of Nebraska Kearney',
        );
    }

    public static function getPossibleDeliveryMethods()
    {
        //Value=>Description
        return array(
            'Classroom'      => 'Classroom',
            'Web'            => 'Online',
            'Correspondence' => 'Correspondence',
        );
    }

    public static function getPossibleTermsOffered()
    {
        //Value=>Description
        return array(
            'Fall'   => 'Fall',
            'Spring' => 'Spring',
            'Summer' => 'Summer',
        );
    }
}
