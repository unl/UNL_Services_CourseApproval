<?php

namespace UNL\Services\CourseApproval\SubjectArea;

class Groups implements \Countable, \IteratorAggregate
{
    /**
     * @var string[]
     */
    protected $groups = array();

    /**
     * @var SubjectArea
     */
    protected $subjectArea;

    public function __construct(SubjectArea $subjectArea)
    {
        $this->subjectArea = $subjectArea;
        $xml = $subjectArea->getCourseXmlObject();
        $xpath = sprintf('//default:subject[.="%s"]/../default:courseGroup', $subjectArea->getSubject());
        $groups = $xml->xpath($xpath);
        if ($groups) {
            foreach ($groups as $group) {
                $this->groups[] = (string)$group;
            }

            $this->groups = array_unique($this->groups);
            asort($this->groups);
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->groups);
    }

    public function count()
    {
        return count($this->groups);
    }
}
