<?php

namespace UNL\Services\CourseApproval\Search;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class AbstractSearch implements SearchInterface
{
    abstract public function aceQuery($ace = null);
    abstract public function subjectAndNumberQuery($subject, $number, $letter = null);
    abstract public function subjectAndNumberPrefixQuery($subject, $number);
    abstract public function subjectAndNumberSuffixQuery($subject, $number);
    abstract public function numberPrefixQuery($number);
    abstract public function numberSuffixQuery($number);
    abstract public function honorsQuery();
    abstract public function titleQuery($title);
    abstract public function subjectAreaQuery($subject);
    abstract public function numberQuery($number, $letter = null);
    abstract public function creditQuery($credits);
    abstract public function prerequisiteQuery($prereq);
    abstract public function intersectQuery($query1, $query2);
    abstract public function graduateQuery();
    abstract public function undergraduateQuery();

    public function filterQuery($query)
    {
        return trim($query);
    }

    public function byTitle($query, $offset = 0, $limit = -1)
    {
        $query = $this->titleQuery($this->filterQuery($query));

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function byNumber($query, $offset = 0, $limit = -1)
    {
        $query = $this->numberQuery($this->filterQuery($query));

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function bySubject($query, $offset = 0, $limit = -1)
    {
        $query = $this->subjectAreaQuery($this->filterQuery($query));

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function byPrerequisite($query, $offset = 0, $limit = -1)
    {
        $query = $this->prerequisiteQuery($query);

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function graduateCourses($offset = 0, $limit = -1)
    {
        $query = $this->graduateQuery();

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function undergraduateCourses($offset = 0, $limit = -1)
    {
        $query = $this->undergraduateQuery();

        return $this->getQueryResult($query, $offset, $limit);
    }

    public function byMany($queries = array(), $offset = 0, $limit = -1)
    {
        $query = $this->determineQuery(array_shift($queries));

        foreach ($queries as $subQuery) {
             $query = $this->intersectQuery($query, $this->determineQuery($subQuery));
        }

        return $this->getQueryResult($query, $offset, $limit);
    }

    /**
     * Helper method to determine the appropriate query based on an input string
     *
     * @return string
     */
    public function determineQuery($query)
    {
        $query = $this->filterQuery($query);

        $driver = $this;

        $facets = array(
            // Credit search
            '/([\d]+)\scredits?/i' => 'creditQuery',

            // ACE course, and number range, eg: ACE 2XX
            '/ace\s*:?\s*([0-9])(X+|\*+)/i' => 'aceAndNumberPrefixQuery',

            // ACE outcome number
            '/ace\s*:?\s*(10|[1-9])/i' => 'aceQuery',

            // ACE course
            '/ace/i' => 'aceQuery',

            // Course subject code and number
            '/([A-Z]{3,4})\s+([\d]?[\d]{2,3})([A-Z])?(:.*)?/i' => function ($matches) use ($driver) {
                $subject = strtoupper($matches[1]);
                $letter = null;

                if (isset($matches[3])) {
                    $letter = $matches[3];
                }

                return $driver->subjectAndNumberQuery($subject, $matches[2], $letter);
            },

            // Course subject and number range, eg: MRKT 3XX
            '/([A-Z]{3,4})\s+([0-9])(X+|\*+)?/i' => function ($matches) use ($driver) {
                $subject = strtoupper($matches[1]);
                return $driver->subjectAndNumberPrefixQuery($subject, $matches[2]);
            },

            // Course subject and number suffix, eg: MUDC *41
            '/([A-Z]{3,4})\s+(X+|\*+)([0-9]+)/i' => function ($matches) use ($driver) {
                $subject = strtoupper($matches[1]);
                return $driver->subjectAndNumberSuffixQuery($subject, $matches[3]);
            },

            '/([\d]?[\d]{2,3})([A-Z])?(\*+)?/i' => function ($matches) use ($driver) {
                $letter = null;

                if (isset($matches[2])) {
                    $letter = $matches[2];
                }

                return $driver->numberQuery($matches[1], $letter);
            },

            '/([0-9])(X+|\*+)?/i' => 'numberPrefixQuery',
            '/(X+|\*+)([0-9]+)?/i' => 'numberSuffixQuery',
            '/([A-Z]{3,4})(\s*:\s*.*)?(\s[Xx]+|\s\*+)?/' => 'subjectAreaQuery',
            '/honors/i' => 'honorsQuery',
            '/(.*)/' => 'titleQuery',
        );

        $queries = array();

        foreach ($facets as $regex => $method) {
            if (preg_match($regex, $query, $matches)) {
                $arg = $matches;
                $function = $method;

                if (!$method instanceof \Closure) {
                    $arg = null;

                    if (isset($matches[1])) {
                        $arg = $matches[1];
                    }

                    $function = array($this, $method);
                }

                $queries[] = call_user_func($function, $arg);

                // Pull this search facet off the query and continue
                $query = trim(str_replace($matches[0], '', $query));

                if ($query == '') {
                    break;
                }
            }
        }

        $query = array_shift($queries);

        foreach ($queries as $subQuery) {
             $query = $this->intersectQuery($query, $subQuery);
        }

        return $query;
    }

    public function byAny($query, $offset = 0, $limit = -1)
    {
        $query = $this->determineQuery($query);

        return $this->getQueryResult($query, $offset, $limit);
    }

    abstract public function getQueryResult($query, $offset = 0, $limit = -1);
}
