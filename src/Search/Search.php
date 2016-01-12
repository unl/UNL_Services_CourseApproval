<?php

namespace UNL\Services\CourseApproval\Search;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Search extends AbstractSearch
{
    /**
     * The driver that performs the searches
     * @var SearchInterface
     */
    protected $driver;

    public function __construct(SearchInterface $driver = null)
    {
        if (!$driver) {
            $driver = new XPath();
        }

        $this->driver = $driver;
    }

    /**
     * Combine two queries into one which will return the intersect
     *
     * @return string
     */
    public function intersectQuery($query1, $query2)
    {
        return $this->driver->intersectQuery($query1, $query2);
    }

    public function aceQuery($ace = null)
    {
        return $this->driver->aceQuery($ace);
    }

    public function aceAndNumberPrefixQuery($number)
    {
        return $this->driver->aceAndNumberPrefixQuery($number);
    }

    public function subjectAndNumberQuery($subject, $number, $letter = null)
    {
        return $this->driver->subjectAndNumberQuery($subject, $number, $letter);
    }

    public function subjectAndNumberPrefixQuery($subject, $number)
    {
        return $this->driver->subjectAndNumberPrefixQuery($subject, $number);
    }

    public function subjectAndNumberSuffixQuery($subject, $number)
    {
        return $this->driver->subjectAndNumberSuffixQuery($subject, $number);
    }

    public function numberPrefixQuery($number)
    {
        return $this->driver->numberPrefixQuery($number);
    }

    public function numberSuffixQuery($number)
    {
        return $this->driver->numberSuffixQuery($number);
    }

    public function honorsQuery()
    {
        return $this->driver->honorsQuery();
    }

    public function titleQuery($title)
    {
        return $this->driver->titleQuery($title);
    }

    public function subjectAreaQuery($subject)
    {
        return $this->driver->subjectAreaQuery($subject);
    }

    public function getQueryResult($query, $offset = 0, $limit = -1)
    {
        return $this->driver->getQueryResult($query, $offset, $limit);
    }

    public function numberQuery($number, $letter = null)
    {
        return $this->driver->numberQuery($number, $letter);
    }

    public function creditQuery($credits)
    {
        return $this->driver->creditQuery($credits);
    }

    public function prerequisiteQuery($prereq)
    {
        return $this->driver->prerequisiteQuery($prereq);
    }

    public function undergraduateQuery()
    {
        return $this->driver->undergraduateQuery();
    }

    public function graduateQuery()
    {
        return $this->driver->graduateQuery();
    }
}
