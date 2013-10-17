<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Config;

/**
 * Config instance with ability to save variables.
 */
abstract class ConfigSavable extends Config
{
    protected $changed = false;
    
    /**
     * @see set()
     */
    public function __set($key, $value)
    {
        return $this -> set($key, $value);
    }
    
    /**
     * Read configuration from JSON file.
     * 
     * @param string
     * @param mixed
     * @return self
     */
    public function set($key, $value)
    {
        $curr = &$this -> config;
        
        foreach (explode('.', $key) as $key) {
            if (!isset($curr[$key])) {
                $curr[$key] = [];
            }
            
            $curr = &$curr[$key];
        }
        
        $curr = $value;
        
        $this -> changed = true;
        
        return $this;
    }
    
    /**
     * Save changes only, if config was changed.
     * 
     * @return self
     */
    public function saveChanged()
    {
        if ($this -> changed) {
            $this -> save();
        }
        
        return $this;
    }
    
    /**
     * Settings save implementation.
     * 
     * This method should set 'changed' field to false.
     * 
     * @return self
     */
    abstract public function save();
}
