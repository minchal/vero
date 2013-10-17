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
     * @return mixed
     */
    public function findAutologinKey($key);
    
    /**
     * Add autologin key for speciefied user.
     * 
     * @param string
     * @param mixed
     */
    public function addAutologinKey($key, $userId);
    
    /**
     * Delete autologin key, if exists.
     * 
     * @param string
     */
    public function removeAutologinKey($key);
}
