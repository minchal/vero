<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\ACL\Backend;

use Vero\ACL\Backend;
use Vero\Cache\Cache;

/**
 * ACL Backend.
 * Provides data for each role.
 * This backend requires Cache instance.
 */
class XML implements Backend
{
    protected $dir;
    protected $cache;
    
    /**
     * Construct backend with cache.
     * Role files searched in $dir
     * 
     * @param string
     */
    public function __construct($dir, Cache $cache)
    {
        $this -> dir   = $dir;
        $this -> cache = $cache;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($role)
    {
        $file = $this->dir . $role . '.xml';
        
        if (!file_exists($file)) {
            return array();
        }
        
        $cacheId = 'acl/'.$role;
        $data = $this -> cache -> fetch($cacheId);
        
        if (!$data || $data['saveTime'] < filemtime($file)) {
            $xml = (array) simplexml_load_file($file);
            $data['data']     = $this -> loadRec(isset($xml['r']) ? $xml['r'] : null);
            $data['saveTime'] = time();
            
            $this -> cache -> save($cacheId, $data);
        }
        
        return $data['data'];
    }
    
    /**
     * Recursive parse XML.
     */
    private function loadRec($items)
    {
        $ret = array();
        if ($items) {
            if (!is_array($items)) {
                $items = array($items);
            }
            
            foreach ($items as $i) {
                $i = (array) $i;
                
                $name   = $i['@attributes']['name'];
                $access = $i['@attributes']['access'] == 'allow';
                
                $ret[$name] = array(
                    'access' => $access,
                    'items' => $this -> loadRec(isset($i['r']) ? $i['r'] : null)
                );
            }
        }
        return $ret;
    }
}
