<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Config;

/**
 * Native PHP configuration file.
 */
class FileArray extends Config
{
    /**
     * Read configuration from native PHP file.
     * 
     * @param string
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new Exception\FileNotFound('Configuration file '.$file.' was not found.');
        }
        
        $config = require($file);
        
        if (!is_array($config)) {
            throw new Exception\BadFormat('Configuration file '.$file.' did not returned array.');
        }
        
        $this -> config = $config;
    }
}
