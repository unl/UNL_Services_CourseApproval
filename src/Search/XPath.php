<?php

namespace UNL\Services\CourseApproval\Search;

use UNL\Services\CourseApproval\Data;
use UNL\Services\CourseApproval\Course\Course;

/**
 *
 * Course search driver which uses XPath queries on the course XML data
 *
 * @author Brett Bieber <brett.bieber@gmail.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class XPath extends AbstractSearch
{
    /**
     * SimpleXMLElement for all courses
     *
     * @var \SimpleXMLElement
     */
    protected static $allCourses;

    protected static $courses = array();

    const XML_BASE = '/default:courses/default:course/';

    /**
     * Get all courses in a SimpleXMLElement
     *
     * @return SimpleXMLElement
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function getCourses()
    {
        if (!isset(static::$allCourses)) {
            $xml = Data::getXCRIService()->getAllCourses();
            static::$allCourses = new \SimpleXMLElement($xml);
            Course::registerXPathNamespaces(static::$allCourses);
        }

        return static::$allCourses;
    }

    /**
     * Get the XML for a specific subject area as a SimpleXMLElement
     *
     * @param string $subjectarea Course subject area e.g. CSCE
     * @return \SimpleXMLElement
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function getSubjectAreaCourses($subjectarea)
    {
        if (!isset(static::$courses[$subjectarea])) {
            $xml = Data::getXCRIService()->getSubjectArea($subjectarea);
            static::$courses[$subjectarea] = new \SimpleXMLElement($xml);
            Course::registerXPathNamespaces(static::$courses[$subjectarea]);
        }

        return static::$courses[$subjectarea];
    }

    /**
     * Utility method to trim out characters which aren't safe for XPath queries
     *
     * @param string $query Search string
     * @return string
     */
    public function filterQuery($query)
    {
        $query = parent::filterQuery($query);
        $query = str_replace(array('/', '"', '\'', '*'), ' ', $query);
        return $query;
    }

    /**
     * Construct a query for courses matching an Achievement Centered Education (ACE) number
     *
     * @param string|int $ace Achievement Centered Education (ACE) number, e.g. 1-10
     *
     * @return string XPath query
     */
    public function aceQuery($ace = null)
    {
        if ($ace) {
            return sprintf('%1$s:aceOutcomes[%1$s:slo="%2$s"]/parent::*', Course::DEFAULT_NS, $ace);
        }
        return Course::DEFAULT_NS . ':aceOutcomes/parent::*';
    }

    /**
     * Construct a query for Achievement Centered Education (ACE) courses which
     * have a course number prefix
     *
     * @param string|int $number Number prefix, e.g. 1 for 100 level ACE courses
     *
     * @return string XPath query
     */
    public function aceAndNumberPrefixQuery($number)
    {
        return sprintf(
            '%1$s:courseCodes/%1$s:courseCode/%1$s:courseNumber[starts-with(., "%2$s")]' .
                '/parent::*/parent::*/parent::*/%1$s:aceOutcomes/parent::*',
            Course::DEFAULT_NS,
            $number
        );
    }

    /**
     * Construct a query for courses matching a subject and number prefix
     *
     * @param string     $subject Subject code, e.g. CSCE
     * @param string|int $number  Course number prefix, e.g. 2 for 200 level courses
     * @return string XPath query
     */
    public function subjectAndNumberPrefixQuery($subject, $number)
    {
        return "default:courseCodes/default:courseCode[starts-with(default:courseNumber, '$number') and default:subject='$subject']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses matching a subject and number suffix
     *
     * @param string     $subject Subject code, e.g. MUDC
     * @param string|int $number  Course number prefix, e.g. 41 for 241, 341, 441
     * @return string XPath query
     */
    public function subjectAndNumberSuffixQuery($subject, $number)
    {
        return "default:courseCodes/default:courseCode[('$number' = substring(default:courseNumber,string-length(default:courseNumber)-string-length('$number')+1)) and default:subject='$subject']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses matching a number prefix
     *
     * @param string|int $number  Course number prefix, e.g. 2 for 200 level courses
     * @return string XPath query
     */
    public function numberPrefixQuery($number)
    {
        return "default:courseCodes/default:courseCode/default:courseNumber[starts-with(., '$number')]/parent::*/parent::*/parent::*";
    }

    /**
     * Construct a query for courses matching a number suffix
     *
     * @param string|int $number  Course number suffix, e.g. 41 for 141, 241, 341 etc
     * @return string XPath query
     */
    public function numberSuffixQuery($number)
    {
        return "default:courseCodes/default:courseCode/default:courseNumber['$number' = substring(., string-length(.)-string-length('$number')+1)]/parent::*/parent::*/parent::*";
    }

    /**
     * Construct a query for honors courses
     *
     * @return string XPath query
     */
    public function honorsQuery()
    {
        return "default:courseCodes/default:courseCode[default:courseLetter='H']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses with a title matching the query
     *
     * @param string $title Portion of the title of the course
     * @return string XPath query
     */
    public function titleQuery($title)
    {
        return 'default:title['.$this->caseInsensitiveXPath($title).']/parent::*';
    }

    /**
     * Construct a query for courses matching a subject area
     *
     * @param string $subject Subject code, e.g. CSCE
     * @return string XPath query
     */
    public function subjectAreaQuery($subject)
    {
        return "default:courseCodes/default:courseCode[default:subject='$subject']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses matching a subject and number
     *
     * @param string     $subject Subject code, e.g. CSCE
     * @param string|int $number  Course number, e.g. 201
     * @param string     $letter  Optional course letter, e.g. H
     * @return string XPath query
     */
    public function subjectAndNumberQuery($subject, $number, $letter = null)
    {
        return "default:courseCodes/default:courseCode[default:courseNumber='$number'{$this->courseLetterCheck($letter)} and default:subject='$subject']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses matching a number
     *
     * @param string|int $number Course number, e.g. 201
     * @param string     $letter Optional course letter, e.g. H
     * @return string XPath query
     */
    public function numberQuery($number, $letter = null)
    {
        return "default:courseCodes/default:courseCode[default:courseNumber='$number'{$this->courseLetterCheck($letter)}]/parent::*/parent::*";
    }

    /**
     * Construct a query for undergraduate courses
     *
     * @return string XPath query
     */
    public function undergraduateQuery()
    {
        return "default:courseCodes/default:courseCode[default:courseNumber<'500']/parent::*/parent::*";
    }

    /**
     * Construct a query for graduate courses
     *
     * @return string XPath query
     */
    public function graduateQuery()
    {
        return "default:courseCodes/default:courseCode[default:courseNumber>='500']/parent::*/parent::*";
    }

    /**
     * Construct part of an XPath query for matching a course letter
     *
     * @param string $letter Letter, e.g. H
     * @return string
     */
    protected function courseLetterCheck($letter = null)
    {
        $letterCheck = '';
        if (!empty($letter)) {
            $letterCheck = " and (default:courseLetter='".strtoupper($letter)."' or default:courseLetter='".strtolower($letter)."')";
        }
        return $letterCheck;
    }

    /**
     * Construct a query for courses with the required number of credits
     *
     * @param string|int $credits Course credits
     * @return string XPath query
     */
    public function creditQuery($credits)
    {
        return "default:courseCredits[default:credit='$credits']/parent::*/parent::*";
    }

    /**
     * Construct a query for courses with prerequisites matching the query
     *
     * @param string $prereq Query to search prereqs for
     * @return string XPath query
     */
    public function prerequisiteQuery($prereq)
    {
        return 'default:prerequisite['.$this->caseInsensitiveXPath($prereq).']/parent::*';
    }

    /**
     * Convert a query to a case-insensitive XPath contains query
     *
     * @param string $query The query to search for
     *
     * @return string
     */
    protected function caseInsensitiveXPath($query)
    {
        $query = strtolower($query);
        return 'contains(translate(.,"ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"),"'.$query.'")';
    }

    /**
     * Combine two XPath queries into one which will return the intersect
     *
     * @return string
     */
    public function intersectQuery($query1, $query2)
    {
        return $query1 . '/' . $query2;
    }

    /**
     * Execute the supplied query and return matching results
     *
     * @param string $query  XPath compatible query
     * @param int    $offset Offset for pagination of search results
     * @param int    $limit  Limit for the number of results returned
     * @return Results
     */
    public function getQueryResult($query, $offset = 0, $limit = -1)
    {
        // prepend XPath XML Base
        $query = static::XML_BASE . $query;

        try {
            $result = static::getCourses()->xpath($query);
        } catch (\Exception $exception) {
            $result = false;
        }

        if ($result === false) {
            $result = array();
        }

        return new Results($result, $offset, $limit);
    }
}
