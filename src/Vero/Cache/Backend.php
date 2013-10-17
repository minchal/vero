<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Cache;

/**
 * Backend for cache interface.
 */
interface Backend
{
    /**
     * Fetches an entry from the cache.
     * 
     * @param string $id
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id);
    
    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id
     * @return boolean
     */
    public function contains($id);
    
    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this cache entry (0=>infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0);
    
    /**
     * Deletes a cache entry.
     * 
     * @param string $id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function delete($id);
}
