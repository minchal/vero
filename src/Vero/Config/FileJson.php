<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Config;

/**
 * JSON configuration file.
 */
class FileJson extends ConfigSavable
{
    protected $file;
    
    /**
     * Read configuration from JSON file.
     * 
     * @param string
     */
    public function __construct($file, $touch = false)
    {
        if (!file_exists($file)) {
            if ($touch) {
                if (!@file_put_contents($file, json_encode([], JSON_PRETTY_PRINT))) {
                    throw new Exception\WriteFail('Configuration file '.$file.' can not be written.');
                }
            } else {
                throw new Exception\FileNotFound('Configuration file '.$file.' was not found.');
            }
        }
        
        $config = json_decode(file_get_contents($file), true);
        
        if (json_last_error()) {
            throw new Exception\BadFormat(
                'Parsing JSON configuration file '.$file.' failed. Error code: '.json_last_error().'.'
            );
        }
        
        $this -> config = (array) $config;
        $this -> file = $file;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save()
    {
        if (!@file_put_contents($this->file, json_encode($this->config, JSON_PRETTY_PRINT))) {
            throw new Exception\WriteFail('Configuration file '.$this->file.' can not be written.');
        }
        
        $this -> changed = false;
        return $this;
    }
}
