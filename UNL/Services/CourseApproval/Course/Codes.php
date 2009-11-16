<?php
class UNL_Services_CourseApproval_Course_Codes implements Countable, Iterator
{
    /**
     * The course codes element.
     * 
     * @var SimpleXMLElement
     */
    protected $_xmlCourseCodes;
    
    protected $_currentCourseCode = 0;
    
    function __construct($courseCodes)
    {
        $this->_xmlCourseCodes = $courseCodes;
    }
    
    function current()
    {
        $number = UNL_Services_CourseApproval_Course::courseNumberFromCourseCode($this->_xmlCourseCodes[$this->_currentCourseCode]);
        return new UNL_Services_CourseApproval_Listing($this->_xmlCourseCodes[$this->_currentCourseCode]->subject,
                                                     $number,
                                                     UNL_Services_CourseApproval_Course::getListingGroups($this->_xmlCourseCodes[$this->_currentCourseCode]));
    }
    
    function next()
    {
        ++$this->_currentCourseCode;
    }
    
    function rewind()
    {
        $this->_currentCourseCode = 0;
    }
    
    function valid()
    {
        if ($this->_currentCourseCode >= $this->count()) {
            return false;
        }
        return true;
    }
    
    function count()
    {
        return count($this->_xmlCourseCodes);
    }
    
    function key()
    {
        return $this->current->courseNumber;
    }
}
?>