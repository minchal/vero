<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Cache;

use Doctrine\Common\Cache\Cache as DoctrineCache;

/**
 * Cache object.
 * 
 * Current implementation is proxy for Doctrine\Common\Cache library.
 */
class Cache
{
    protected $backend;
    protected $ns;
    
    /**
     * Construct Cache with specified Backend.
     * 
     * @param \Common\Cache\Backend|Doctrine\Common\Cache\Cache
     * @param string Namespace for this cache instance
     */
    public function __construct($backend, $ns = null)
    {
        if (!$backend instanceof DoctrineCache && !$backend instanceof Backend) {
            throw new \DomainException(
                'Cache backend class must implement Vero\Cache\Backend or Doctrine\Common\Cache\Cache!'
            );
        }
        
        $this -> backend = $backend;
        $this -> ns      = $ns;
    }
    
    /**
     * Set the namespace to prefix all cache ids with.
     *
     * @param string $ns
     */
    public function setNamespace($ns)
    {
        $this -> ns = (string) $ns;
    }
    
    /**
     * Get the namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this -> ns;
    }
    
    /**
    * Prefix the passed id with the configured namespace value
    *
    * @param string
    * @return string
    */
    protected function getNamespacedId($id)
    {
        return $this -> ns . $id;
    }
    
    /**
     * Fetches an entry from the cache.
     * 
     * @param string
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        return $this -> backend -> fetch($this -> getNamespacedId($id));
    }
    
    /**
     * Test if an entry exists in the cache.
     *
     * @param string
     * @return boolean
     */
    public function contains($id)
    {
        return $this -> backend -> contains($this -> getNamespacedId($id));
    }
    
    /**
     * Puts data into the cache.
     *
     * @param string The cache id.
     * @param string The cache entry/data.
     * @param int The lifetime. If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this -> backend -> save($this -> getNamespacedId($id), $data, $lifeTime);
    }
    
    /**
     * Deletes a cache entry.
     * 
     * @param string $id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function delete($id)
    {
        return $this -> backend -> delete($this -> getNamespacedId($id));
    }
}
