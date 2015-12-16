<?php

namespace UNL\Services\CourseApproval\Course;

class Activities implements \Countable, \Iterator
{
    protected $xmlActivities;

    protected $currentActivity = 0;

    public static function getPossibleActivities()
    {
        //Value=>Description
        return array(
            'lec' => 'Lecture',
            'lab' => 'Lab',
            'stu' => 'Studio',
            'fld' => 'Field',
            'quz' => 'Quiz',
            'rct' => 'Recitation',
            'ind' => 'Independent Study',
            'psi' => 'Personalized System of Instruction',
        );
    }

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xmlActivities = $xml;
    }

    public function current()
    {
        return $this->xmlActivities[$this->currentActivity];
    }

    public function next()
    {
        ++$this->currentActivity;
    }

    public function rewind()
    {
        $this->currentActivity = 0;
    }

    public function valid()
    {
        if ($this->currentActivity >= $this->count()) {
            return false;
        }
        return true;
    }

    public function key()
    {
        return (string)$this->current()->type;
    }

    public function count()
    {
        return count($this->xmlActivities);
    }

    public static function getFullDescription($activity)
    {
        $activities = static::getPossibleActivities();
        if (!isset($activities[$activity])) {
            throw new \Exception('Unknown activity type! '.$activity);
        }

        return $activities[$activity];
    }
}
