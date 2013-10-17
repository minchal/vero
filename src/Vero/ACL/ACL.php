<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\ACL;

/**
 * Access Control List.
 */
class ACL
{
    protected $data = array();
    protected $backend;
    protected $sessionRole;
    
    /**
     * Construct ACL with specified backend
     */
    public function __construct(Backend $backend)
    {
        $this -> backend = $backend;
    }
    
    /**
     * Set role for current session.
     * 
     * @return self
     */
    public function setSessionRole(Role $role)
    {
        $this -> sessionRole = $role;
        return $this;
    }
    
    /**
     * Returns true if role have access to key.
     * 
     * @param string|array
     * @param null|Role
     * @return boolean
     */
    public function check($key, $role = null)
    {
        if ($role === null) {
            $role = $this -> sessionRole;
        }
        
        if (!$role instanceof Role) {
            throw new \InvalidArgumentException('Role must be instance of Vero\ALC\Role.');
        }
        
        $role = $role -> getRole();
        
        if (!isset($this -> data[$role])) {
            $this -> data[$role] = $this -> backend -> get($role);
        }
        
        if (!is_array($key)) {
            $key = explode('/', $key);
        }
        
        // search key
        $next = & $this -> data[$role];
        $last = false;
        
        foreach ($key as $part) {
            if (isset($next[$part])) {
                if (!$next[$part]['access']) {
                    return false;
                }
                
                $last = $next[$part]['access'];
                $next = & $next[$part]['items'];
                
            } else {
                return isset($next['*']) ? $next['*']['access'] : false;
            }
        }
        
        return $last;
    }
}
