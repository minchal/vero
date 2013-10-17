<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Auth;

/**
 * Interface for class, that provides instances of User.
 * User is identified by ID.
 */
interface UserProvider
{
    /**
     * Get instance of User.
     * If user cannot be found null can be returned.
     * 
     * @param mixed $id
     * @return User|null
     */
    public function getUser($id);

    /**
     * Get instance of guest User.
     * 
     * @return User
     */
    public function getDefaultUser();
    
    /**
     * Refresh (or log) last visit info.
     * 
     * @param mixed
     * @param \DateTime
     * @param string
     * @param string
     */
    public function registerVisit($id, \DateTime $time, $url, $ip);
}
