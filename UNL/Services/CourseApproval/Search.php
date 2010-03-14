<?php
class UNL_Services_CourseApproval_Search
{
    /**
     * SimpleXMLElement for all courses
     * 
     * @var SimpleXMLElement
     */
    protected static $courses;
    
    protected static function getCourses()
    {
        if (!isset(self::$courses)) {
            $xml = UNL_Services_CourseApproval::getXCRIService()->getAllCourses();
            self::$courses = new SimpleXMLElement($xml);

            //Fetch all namespaces
            $namespaces = self::$courses->getNamespaces(true);
            self::$courses->registerXPathNamespace('default', $namespaces['']);

            //Register the rest with their prefixes
            foreach ($namespaces as $prefix => $ns) {
                self::$courses->registerXPathNamespace($prefix, $ns);
            }
        }

        return self::$courses;
    }
    
    public function setCourses(SimpleXMLElement $courses)
    {
        self::$courses = $courses;
    }
    
    public function byNumber($number)
    {
        $parts = array();
        if (!UNL_Services_CourseApproval_Course::validCourseNumber($number, $parts)) {
            throw new Exception('Invalid course number format '.$number);
        }

        if (!empty($parts['courseLetter'])) {
            $letter_check = "default:courseLetter='{$parts['courseLetter']}'";
        } else {
            $letter_check = 'not(default:courseLetter)';
        }

        $xpath = "/default:courses/default:course/default:courseCodes/default:courseCode[default:courseNumber='{$parts['courseNumber']}' and $letter_check]/parent::*/parent::*";
        return new UNL_Services_CourseApproval_Courses(self::getCourses()->xpath($xpath));
    }
    
    public function bySubject($subject)
    {
        $subject = strtoupper(trim($subject));
        if (!preg_match('/^([A-Z]{3,4})$/', $subject)) {
            throw new Exception('Invalid subject format '.$subject);
        }

        $xpath = "/default:courses/default:course/default:courseCodes/default:courseCode[default:subject='$subject']/parent::*/parent::*";
        return new UNL_Services_CourseApproval_Courses(self::getCourses()->xpath($xpath));

    }
    
    public function byTitle($title)
    {
        $title = trim($title);

        $xpath = "/default:courses/default:course/default:title[contains(.,'$title')]/parent::*";
        return new UNL_Services_CourseApproval_Courses(self::getCourses()->xpath($xpath));

    }

    public function byAny($query)
    {
        
        $xpath = '';

        $query = trim($query);

        $query = str_replace(array('/', '"', '\'', '*'), ' ', $query);

        switch(true) {
            case preg_match('/^([A-Z]{3,4})\s+([0-9]{2,3}[A-Z]?)$/i', $query, $matches):
                $subject = strtoupper($matches[1]);
                $num_parts = array();
                UNL_Services_CourseApproval_Course::validCourseNumber($matches[2], $num_parts);
                $letter_check = '';
                if (!empty($num_parts['courseLetter'])) {
                    $letter_check = " and default:courseLetter='{$num_parts['courseLetter']}'";
                }
                $xpath .= "/default:courses/default:course/default:courseCodes/default:courseCode[default:courseNumber='{$num_parts['courseNumber']}'$letter_check and default:subject='$subject']/parent::*/parent::*";
                break;
            case preg_match('/^([0-9]{2,3}[A-Z]?)$/', $query):
                $num_parts = array();
                UNL_Services_CourseApproval_Course::validCourseNumber($query, $num_parts);

                $letter_check = '';
                if (!empty($num_parts['courseLetter'])) {
                    $letter_check = " and default:courseLetter='{$num_parts['courseLetter']}'";
                }

                $xpath .= "/default:courses/default:course/default:courseCodes/default:courseCode[default:courseNumber='{$num_parts['courseNumber']}'$letter_check]/parent::*/parent::*";
                break;
            case preg_match('/^([A-Z]{3,4})$/i', $query):
                $xpath .= "/default:courses/default:course/default:courseCodes/default:courseCode[default:subject='$query']/parent::*/parent::*";
                break;
            default:
                // Do a title text search
                $xpath .= "/default:courses/default:course/default:title[contains(.,'$query')]/parent::*";
        }

        $result = self::getCourses()->xpath($xpath);

        if (!$result) {
            $result = array();
        }

        return new UNL_Services_CourseApproval_Courses($result);

    }
}