<?php

namespace UNL\Services\CourseApproval\Course;

/**
 * Collection of course codes for this course
 *
 * @author Brett Bieber <brett.bieber@gmail.com>
 */
class Codes extends \ArrayIterator
{
    protected $course;

    /**
     * Array of results, usually from an xpath query
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $courseCodes = $course->getXmlObject()->courseCodes->children();
        $codes = array();

        foreach ($courseCodes as $code) {
            $codes[] = $code;
        }

        parent::__construct($codes);
    }

    /**
     * @return Listing
     */
    public function current()
    {
        $codeXml = parent::current();
        return new Listing($this->course, $codeXml);
    }

    /**
     * Get the course number
     *
     * @return string course number
     */
    public function key()
    {
        return (string) $this->current()->courseNumber;
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function append($value)
    {
        throw new \Exception('This is a readonly collection.');
    }

    public function offsetExists($index)
    {
        if ($this->offsetGet($index)) {
            return true;
        }

        return false;
    }

    public function offsetGet($index)
    {
        $xmlCodes = $this->getArrayCopy();
        $matchingListings = array();

        foreach ($xmlCodes as $codeXml) {
            if ((string) $codeXml['type'] === $index) {
                $matchingListings[] = new Listing($this->course, $codeXml);
            }
        }

        return $matchingListings;
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($index, $newval)
    {
        throw new \Exception('This is a readonly collection.');
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($index)
    {
        throw new \Exception('This is a readonly collection.');
    }
}
