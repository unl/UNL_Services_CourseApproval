<?php 
class UNL_Services_CourseApproval_SubjectArea
{
    public $subject;
    
    public $courses;
    
    function __construct($subject)
    {
        $this->subject = $subject;
        $this->courses = new UNL_Services_CourseApproval_SubjectArea_Courses($this);
    }
    
    function __toString()
    {
        return $this->subject;
    }
}
?>