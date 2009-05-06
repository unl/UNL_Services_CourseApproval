<?php 
class UNL_Services_CourseApproval_Listing
{
    /**
     * The course associated with this listing.
     * 
     * @var UNL_Services_CourseApproval_Course
     */
    public $course;
    
    /**
     * Internal subject area object
     * 
     * @var UNL_Services_CourseApproval_SubjectArea
     */
    protected $_subjectArea;
    
    /**
     * The subject area for this listing eg ACCT
     * 
     * @var string
     */
    public $subjectArea;
    
    /**
     * The course number eg 201
     * 
     * @var string|int
     */
    public $courseNumber;
    
    function __construct($subject, $number)
    {
        $this->_subjectArea = new UNL_Services_CourseApproval_SubjectArea($subject);
        $this->subjectArea  = $this->_subjectArea->subject;
        $this->course       = &$this->_subjectArea->courses[$number];
        $this->courseNumber = $number;
    }
    
    function __get($var)
    {
        // Delegate to the course
        return $this->course->$var;
    }
    
    function __isset($var)
    {
        // Delegate to the course
        return isset($this->course->$var);
    }
}
?>