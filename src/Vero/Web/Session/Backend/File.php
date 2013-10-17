<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Session\Backend;

use Vero\Web\Session\Backend;

/**
 * Sessions in files.
 */
class File implements Backend
{
    protected $dir;
    
    /**
     * Construct backend in directory.
     * 
     * @params string $dir
     */
    public function __construct($dir)
    {
        $this -> dir = $dir;
        \Vero\Filesystem\Directory::ensure($dir);
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($id, $ttl)
    {
        $file = $this->dir . $id . '.session';
        
        if (!file_exists($file) || filemtime($file) < time()-$ttl) {
            return false;
        }
        
        $retries = 0;
        
        do {
            $content = file_get_contents($file);
            
            if (!$content) {
                usleep(rand(100,10000));
            }
            
            $retries++;
        } while (!$content && $retries < 100);
        
        if (!$content) {
            return false;
        }
        
        return unserialize($content);
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($id, array $data, $ttl)
    {
        $file = $this->dir . $id . '.session';
        
        \Vero\Filesystem\Directory::ensure($this -> dir);
        
        file_put_contents($file, serialize($data), LOCK_EX);
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $file = $this->dir . $id . '.session';
        
        if (!file_exists($file)) {
            return false;
        }
        
        return unlink($file);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear($ttl)
    {
        $time = time() - $ttl;
        
        foreach (glob($this -> dir.'*.session') as $file) {
            if (filemtime($file) < $time) {
                unlink($file);
            }
        }
    }
}
