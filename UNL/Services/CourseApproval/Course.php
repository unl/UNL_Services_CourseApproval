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
    
    protected $_getMap = array('credits'=>'getCredits',
                               'dfRemoval'=>'getDFRemoval');
    
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
        if (array_key_exists($var, $this->_getMap)) {
            return $this->{$this->_getMap[$var]}();
        }
        
        if ($this->_internal->$var->children()) {
            $string = '';
            foreach ($this->_internal->$var->children() as $el) {
                $string .= (string)$el;
            }
            return $string;
        }
        return (string)$this->_internal->$var;
    }
    
    function getCredits()
    {
        return new UNL_Services_CourseApproval_Course_Credits($this->_internal->credits->children());
    }
    
    function getDFRemoval()
    {
        if ($this->_internal->dfRemoval == 'true') {
            return true;
        }
        
        return false;
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
