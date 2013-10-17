<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Session;

/**
 * Session backend. Operations:
 *  - load session
 *  - save session
 *  - delete session
 * 
 * TTL: Amount of seconds that session is valid.
 */
interface Backend
{
    /**
     * Try to load data for ID.
     * 
     * @param string $id
     * @param int $ttl
     * @return false|array
     */
    public function load($id, $ttl);
    
    /**
     * Save session data for ID.
     * If key exists only update.
     * 
     * @param string $id
     * @param array $data
     * @param int $ttl
     * @return boolean
     */
    public function save($id, array $data, $ttl);
    
    /**
     * Delete session of speciefied ID.
     * 
     * @param string $id
     */
    public function delete($id);
    
    /**
     * Clear expired sessions.
     * (older than TTL)
     * 
     * @param int $ttl
     */
    public function clear($ttl);
}
