<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Filesystem;

/**
 * File in filesystem.
 */
class File extends Node
{
    /**
     * Get file contents.
     * 
     * @return string
     */
    public function getContents()
    {
        $content = @file_get_contents($this -> getPathname());
        
        if ($content === false) {
            throw new Exception(
                sprintf(
                    'File "%s" cannot be readed (%s).',
                    $this->getPathname(),
                    error_get_last()['message']
                )
            );
        }
        
        return $content;
    }
    
    /**
     * Put file content.
     * 
     * @return self
     */
    public function putContents($content)
    {
        if (!@file_put_contents($this -> getPathname(), $content)) {
            throw new Exception(
                sprintf(
                    'File "%s" cannot be written (%s).',
                    $this->getPathname(),
                    error_get_last()['message']
                )
            );
        }
        
        return $this;
    }
    
    /**
     * Copy file.
     * 
     * @return self
     */
    public function copy($to)
    {
        if (!@copy($this -> getPathname(), $to)) {
            throw new Exception(
                sprintf(
                    'File "%s" cannot be copied to "%s" (%s).',
                    $this->getPathname(),
                    $to,
                    error_get_last()['message']
                )
            );
        }
        
        return $this;
    }
    
    /**
     * Delete file.
     * 
     * @return self
     */
    public function delete()
    {
        if (!@unlink($this -> getPathname())) {
            throw new Exception(
                sprintf(
                    'File "%s" cannot be deleted (%s).',
                    $this->getPathname(),
                    error_get_last()['message']
                )
            );
        }
        
        return $this;
    }
}
