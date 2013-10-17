<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Config;

/**
 * Abstract configuration container.
 */
abstract class Config
{
    protected $config = array();
    
    /**
     * @see get()
     */
    public function __get($key)
    {
        return $this -> get($key);
    }
    
    /**
     * Check if value is set.
     * 
     * @param mixed
     * @return boolean
     */
    public function __isset($key)
    {
        return $this -> get($key) !== null;
    }
    
    /**
     * Try to get value or return default value if not found.
     * 
     * Example:
     *    $config -> get('db.hostname', 'localhost');
     * 
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $success = false;
        $found   = $this -> search(explode('.', $key), $success);
        
        if (!$success) {
            return $default;
        }
        
        return $found;
    }
    
    /**
     * Get configuration value at specified index.
     * Throw Exception on failure.
     * 
     * Example:
     *    $config -> get('db.hostname');
     * 
     * @param string
     * @return mixed
     * @throws Vero\Config\Exception\VariableNotFound
     */
    public function fetch($key)
    {
        $success = false;
        $found   = $this -> search(explode('.', $key), $success);
        
        if (!$success) {
            throw new Exception\VariableNotFound('Variable at key "'.$key.'" was not found.');
        }
        
        return $found;
    }
    
    /**
     * Get value at index.
     */
    protected function search($keys, &$success)
    {
        $curr = &$this -> config;
        
        foreach ($keys as $key) {
            if (!isset($curr[$key])) {
                $success = false;
                return null;
            }
            
            $curr = &$curr[$key];
        }
        
        $success = true;
        return $curr;
    }
}
