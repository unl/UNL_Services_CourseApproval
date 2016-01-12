<?php

namespace UNL\Services\CourseApproval\Course;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Credits implements \Countable, \Iterator, \ArrayAccess
{
    protected $xmlCredits;

    protected $currentCredit = 0;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xmlCredits = $xml;
    }

    public function current()
    {
        return $this->xmlCredits[$this->currentCredit];
    }

    public function next()
    {
        ++$this->currentCredit;
    }

    public function rewind()
    {
        $this->currentCredit = 0;
    }

    public function valid()
    {
        if ($this->currentCredit >= $this->count()) {
            return false;
        }
        return true;
    }

    public function count()
    {
        return count($this->xmlCredits);
    }

    public function key()
    {
        $credit = $this->current();
        return $credit['creditType'];
    }

    public function offsetExists($type)
    {
        foreach ($this->xmlCredits as $credit) {
            if ($credit['type'] == $type) {
                return true;
            }
        }
        return false;
    }

    public function offsetGet($type)
    {
        foreach ($this->xmlCredits as $credit) {
            if ($credit['type'] == $type) {
                return (int)$credit;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($type, $var)
    {
        throw new \Exception('Not available.');
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($type)
    {
        throw new \Exception('Not available.');
    }
}
