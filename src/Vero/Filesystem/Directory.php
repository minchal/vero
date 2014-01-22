<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Filesystem;

use IteratorAggregate;
use FilesystemIterator;

/**
 * Directory in filesystem.
 */
class Directory extends Node implements IteratorAggregate
{
    /**
     * Check if writable directory exists or create one.
     * 
     * @param string
     * @return true
     * @throws Exception
     */
    public static function ensure($dir)
    {
        if (!file_exists($dir) || !is_writable($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new Exception(
                    sprintf(
                        'Directory "%s" is not writable or not exists and can not be created.',
                        $dir
                    )
                );
            }
        }
        
        return true;
    }
    
    /**
     * Try to ensure writable directory.
     * 
     * @param string
     * @retrun boolean true, when directory is available
     */
    public static function tryEnsure($dir)
    {
        try {
            return self::ensure($dir);
        } catch (Exception $e) {
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $i = new FilesystemIterator($this -> getRealPath());
        $i -> setInfoClass('Vero\Filesystem\Node');
        return $i;
    }
    
    /**
     * Create directory.
     * 
     * @return self
     */
    public function create()
    {
        if (!@mkdir($this->getPathname(), 0777, true)) {
            throw new Exception(
                sprintf(
                    'Directory "%s" can not be created (%s).',
                    $this->getPathname(),
                    error_get_last()['message']
                )
            );
        }
        return $this;
    }
    
    /**
     * Recursive copy of directory with contents.
     * 
     * @return self
     */
    public function copy($to)
    {
        @mkdir($to, 0777, true);
        
        foreach ($this as $child) {
            $child -> getSpecialized() -> copy($to .'/'. $child->getFilename());
        }
        
        return $this;
    }
    
    /**
     * Recursive delete of directory with contents.
     * 
     * @return self
     */
    public function delete()
    {
        foreach ($this as $child) {
            $child -> getSpecialized() -> delete();
        }
        
        if (!@rmdir($this -> getPathname())) {
            throw new Exception(
                sprintf(
                    'Directory "%s" cannot be deleted (%s).',
                    $this->getPathname(),
                    error_get_last()['message']
                )
            );
        }
        
        return $this;
    }
}
