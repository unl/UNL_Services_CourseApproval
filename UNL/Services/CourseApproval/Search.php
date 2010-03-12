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

        $xpath = "//default:courses/default:course/default:courseCodes/default:courseCode[default:courseNumber='{$parts['courseNumber']}' and $letter_check]/parent::*/parent::*";
        return new UNL_Services_CourseApproval_Courses(self::getCourses()->xpath($xpath));
    }
}