<?php 
class UNL_Services_CourseApproval_SubjectArea_Courses implements ArrayAccess, Countable, Iterator
{
    /**
     * The XCRI as a SimpleXMLElement
     * 
     * @var SimpleXMLElement
     */
    protected $_xcri;
    
    protected $_subjectArea;
    
    protected $_xmlCourses;
    
    protected $_currentXMLCourse = 0;
    
    function __construct(UNL_Services_CourseApproval_SubjectArea $subjectarea)
    {
        $this->_subjectArea = $subjectarea;
        $this->_xcri = new SimpleXMLElement(UNL_Services_CourseApproval::getXCRIService()->getSubjectArea($subjectarea->subject));
        
        //Fetch all namespaces
        $namespaces = $this->_xcri->getNamespaces(true);
        $this->_xcri->registerXPathNamespace('default', $namespaces['']);
        
        //Register the rest with their prefixes
        foreach ($namespaces as $prefix => $ns) {
            $this->_xcri->registerXPathNamespace($prefix, $ns);
        }
        
        $this->rewind();
    }
    
    function rewind()
    {
        $this->_xmlCourses = $this->_xcri->xpath('//default:courses/default:course');
        $this->_currentXMLListing = 0;
    }
    
    function offsetExists($number)
    {
        throw new Exception('Not implemented yet');
    }
    
    function offsetGet($number)
    {
        $parts = array();
        if (!self::validCourseNumber($number, $parts)) {
            throw new Exception('Invalid course number format '.$number);
        }
        
        if (!empty($parts['courseLetter'])) {
            $letter_check = "default:courseLetter='{$parts['courseLetter']}'";
        } else {
            $letter_check = 'not(default:courseLetter)';
        }
        
        $xpath = "//default:courses/default:course/default:courseCodes/default:courseCode[default:subject='{$this->_subjectArea->subject}' and default:courseNumber='{$parts['courseNumber']}' and $letter_check]/parent::*/parent::*";
        $courses = $this->_xcri->xpath($xpath);

        if (count($courses) > 1) {
            // Whoah whoah whoah, more than one course?
            throw new Exception('More than one course was found matching '.$this->_subjectArea->subject.' '.$number);
        }
        
        return new UNL_Services_CourseApproval_Course($courses[0]);
    }
    
    /**
     * Verifies that the course number is in the correct format.
     * 
     * @param $number The course number eg 201H, 4004I
     * @param $parts  Array of matched parts
     * 
     * @return bool
     */
    public static function validCourseNumber($number, &$parts = null)
    {
        $matches = array();
        if (preg_match('/^([\d]?[\d]{2,3})([A-Za-z])?$/', $number, $matches)) {
            $parts['courseNumber'] = $matches[1];
            if (isset($matches[2])) {
                $parts['courseLetter'] = $matches[2];
            }
            return true;
        }
        
        return false;
    }
    
    function offsetSet($number, $value)
    {
        throw new Exception('Not implemented yet');
    }
    
    function offsetUnset($number)
    {
        throw new Exception('Not implemented yet');
    }
    
    function count()
    {
        return count($this->_xcri->xpath('//default:courses/default:course'));
    }
    
    function current()
    {
        return new UNL_Services_CourseApproval_Course(current($this->_xmlCourses));
    }
    
    function next()
    {
        ++$this->_currentXMLCourse;
        return next($this->_xmlCourses);
    }
    
    function key()
    {
        return $this->_currentXMLCourse;
    }
    
    function valid()
    {
        if ($this->_currentXMLCourse >= $this->count()) {
            return false;
        }
        return true;
    }
}
?>
