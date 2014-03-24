<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

/**
 * Shortcuts to use default auth and ACL services and user instance.
 * 
 * Requires: DITrait, EcxeptionsTrait
 */
trait UserTrait
{
    /**
     * Check current user's access to this action 
     * and throw exception in case of fail.
     * 
     * If key is unspecified, get Route ID of current request action.
     * 
     * @param string|null
     * @return true
     * @throws \Vero\Web\Exception\AccessDenied
     */
    public function aclCheck($key = null)
    {
        if (!$this -> acl($key)) {
            throw $this -> accessDenied($key);
        }
        
        return true;
    }
    
    /**
     * Check current user's access to this action.
     * 
     * If key is unspecified, get Route ID of current request action.
     * 
     * @param string|null
     * @return booelan
     */
    public function acl($key = null)
    {
        if (!$key) {
            $key = $this -> get('request') -> action;
        }
        
        return $this -> get('acl') -> check($key);
    }
    
    /**
     * Get current authorized user.
     * 
     * @return \Vero\Web\Auth\User
     */
    public function user()
    {
        return $this -> get('auth') -> getUser();
    }
}
