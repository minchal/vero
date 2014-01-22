<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Auth;

/**
 * Autologin functionality Provider for authorization Manager.
 */
interface AutologinProvider
{
    /**
     * Search autologin key and return user ID or null.
     * 
     * @param string
     * @param int
     * @return mixed
     */
    public function find($key, $ttl);
    
    /**
     * Add autologin key for speciefied user.
     * 
     * @param string
     * @param int
     * @param mixed
     */
    public function add($key, $userId, $ttl);
    
    /**
     * Delete autologin key, if exists.
     * 
     * @param string
     */
    public function delete($key);
}
