<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Filesystem;

use SplFileInfo;

/**
 * Instance of Node (File or Directory - unknown).
 */
class Node extends SplFileInfo
{
    /**
     * Get specialized implementation of node.
     * 
     * @return Directory|File
     */
    public function getSpecialized()
    {
        if ($this -> isDir()) {
            return new Directory($this->getPathname());
        }
        
        return new File($this->getPathname());
    }
    
    /**
     * Check if node exist.
     * 
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->getPathname());
    }
    
    /**
     * Check if node is writable or if not exists is posible to create.
     * 
     * Method runs recursively.
     * 
     * @return boolean
     */
    public function writable()
    {
        if ($this -> exists()) {
            return $this -> isWritable();
        }
        
        $dir = $this -> getPath();
        return $dir ? (new Directory($dir)) -> writable() : false;
    }
    
    /**
     * Check if node is in other directory
     * 
     * @return boolean
     */
    public function in($path)
    {
        $path = realpath($path);
        return $path == $this->getRealPath() || strpos($this->getRealPath(), $path . DIRECTORY_SEPARATOR) === 0;
    }
    
    /**
     * Move node with contents.
     * 
     * @return self
     */
    public function move($to, $name = null)
    {
        if ($name === null) {
            $name = $this -> getBasename();
        }
        
        $to .= '/'.$name;
        
        Directory::ensure(dirname($to));
        
        if (!@rename($this -> getPathname(), $to)) {
            throw new Exception(
                sprintf(
                    'File "%s" cannot be moved to "%s" (%s).',
                    $this->getPathname(),
                    $to,
                    error_get_last()['message']
                )
            );
        }
        
        $c = get_class($this);
        return new $c($to);
    }
    
    /**
     * Change name of file.
     * 
     * @return self
     */
    public function rename($name)
    {
        return $this -> move($this -> getPath(), $name);
    }
    
    /**
     * Get file MIME.
     * 
     * @return string
     */
    public function mime()
    {
        $finfo = new \finfo(FILEINFO_MIME);
        return $finfo -> file($this -> getPathname());
    }
    
    /**
     * Get file MIME type.
     * 
     * @return string
     */
    public function mimeType()
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo -> file($this -> getPathname());
    }
    
    /**
     * Check if file has concrete MIME type or one of type.
     * 
     * @param string|array
     * @return boolean
     */
    public function is($mimes)
    {
        $mimes = (array) $mimes;
        $mime = $this -> mimeType();
        
        foreach ($mimes as $m) {
            if ($m == $mime) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if file is jpeg, gif or png.
     * 
     * @return boolean
     */
    public function isImage()
    {
        return $this -> is(['image/jpeg', 'image/png', 'image/gif']);
    }
    
    /**
     * 
     * @return string
     */
    public function getSizeHum()
    {
        $size = $this -> getSize();
        $i = 0;
        $iec = array(' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        
        while (($size/1024)>1) {
            $size = $size/1024;
            $i++;
        }
        
        return number_format($size, 2, '.', '') . $iec[$i];
    }
}
