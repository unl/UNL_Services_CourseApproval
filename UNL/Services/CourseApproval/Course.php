<?php 
class UNL_Services_CourseApproval_Course
{
    
    /**
     * The internal object
     * 
     * @var SimpleXMLElement
     */
    protected $_internal;
    
    public $codes;
    
    function __construct(SimpleXMLElement $xml)
    {
        $this->_internal = $xml;
        //Fetch all namespaces
        $namespaces = $this->_internal->getNamespaces(true);
        $this->_internal->registerXPathNamespace('default', $namespaces['']);
        
        //Register the rest with their prefixes
        foreach ($namespaces as $prefix => $ns) {
            $this->_internal->registerXPathNamespace($prefix, $ns);
        }
        $this->codes = new UNL_Services_CourseApproval_Course_Codes($this->_internal->courseCodes->children());
    }
    
    function __get($var)
    {
        if ($this->_internal->$var->children()) {
            $string = '';
            foreach ($this->_internal->$var->children() as $el) {
                $string .= (string)$el;
            }
            return $string;
        }
        return (string)$this->_internal->$var;
    }
    
    public static function courseNumberFromCourseCode(SimpleXMLElement $xml)
    {
        $number = (string)$xml->courseNumber;
        if (isset($xml->courseLetter)) {
            $number .= (string)$xml->courseLetter;
        }
        return $number;
    }
}
