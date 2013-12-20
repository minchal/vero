<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\Filesystem\File;
use Vero\Filesystem\Directory;

/**
 * Instance of uploaded file.
 */
class UploadedFile extends File
{
    protected $tmp_name;
    protected $name;
    
    protected $isMoved = false;
    
    /**
     * Try to create valid uploaded file instance.
     * 
     * @param type $tmp_name
     * @param type $name
     * @throws \Vero\Web\Exception\Upload
     */
    public function __construct($tmp_name, $name, $error)
    {
        if ($error != UPLOAD_ERR_OK) {
            throw new Exception\Upload('upload error '.$error, 'global');
        }
        
        if (!is_uploaded_file($tmp_name)) {
            throw new Exception\Upload('upload error invalid', 'global');
        }
        
        $this -> tmp_name = $tmp_name;
        $this -> name = $name;
        
        parent::__construct($tmp_name);
    }
    
    /**
     * Get original name of the file on the client machine.
     * 
     * @return string
     */
    public function getOriginalName()
    {
        return $this -> name;
    }
    
    /**
     * Get original file name extension on the client machine.
     * 
     * @return string
     */
    public function getOriginalExtension()
    {
        return pathinfo($this -> name, PATHINFO_EXTENSION);
    }
    
    /**
     * Move uploaded file to directory.
     * If no new name is speciefied, original file name will be used.
     * 
     * @param string
     * @param string
     * @return self
     */
    public function move($to, $name = null)
    {
        if ($this -> isMoved) {
            return parent::move($to, $name);
        }
        
        $name = $name === null ? $this -> getOriginalName() : $name;
        
        $path = rtrim($to .'/'. $name, '/');
        
        Directory::ensure(dirname($path));
        
        if (!@move_uploaded_file($this->tmp_name, $path)) {
            throw new Exception\Upload('upload error move', 'global');
        }
        
        $this -> isMoved = true;
        
        parent::__construct($path);
        
        return $this;
    }
}
