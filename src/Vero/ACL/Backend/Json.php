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
class Json implements Backend
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
        $file = $this -> dir . $role . '.json';
        
        if (!file_exists($file)) {
            return [];
        }
        
        $cacheId = 'acl/'.$role;
        $data = $this -> cache -> fetch($cacheId);
        
        if (!$data || $data['saveTime'] < filemtime($file)) {
            $data = json_decode(file_get_contents($file), true);
            
            if (json_last_error()) {
                throw new \UnexpectedValueException(
                    'Parsing JSON ACL file '.$file.' failed. Error code: '.json_last_error().'.'
                );
            }
            
            $data['data']     = (array) $data;
            $data['saveTime'] = time();
            
            $this -> cache -> save($cacheId, $data);
        }
        
        return $data['data'];
    }
}
