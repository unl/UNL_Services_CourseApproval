<?php 
class UNL_Services_CourseApproval_CachingService_CacheLite implements UNL_Services_CourseApproval_CachingService
{
    protected $cache;
    
    function __construct()
    {
        require_once 'Cache/Lite.php';
        $options = array('lifeTime'=>604800); //one week lifetime
        $this->cache = new Cache_Lite();
    }
    
    function save($key, $data)
    {
        return $this->cache->save($data, $key, 'ugbulletin');
    }
    
    function get($key)
    {
        if ($data = $this->cache->get($key, 'ugbulletin')) {
            return $data;
        }
        return false;
    }
}
?>