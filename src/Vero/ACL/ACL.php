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
    protected $data = [];
    protected $backend;
    protected $sessionRole;
    
    protected $last = [];
    
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
     * @param null|string|Role
     * @return boolean
     */
    public function check($key, $role = null)
    {
        $role = $this -> getRole($role === null ? $this -> sessionRole : $role);
        
        if (is_array($key)) {
            $keyS = implode('/', $key);
        } else {
            $keyS = $key;
            $key = explode('/', $key);
        }
        
        if (!isset($this -> last[$role][$keyS])) {
            $this -> last[$role][$keyS] = $this -> doCheck($key, $role);
        }
        
        return $this -> last[$role][$keyS];
    }
    
    /**
     * Real check algorithm implementation.
     * 
     * @param array $key
     * @param string $role
     * @return boolean
     */
    protected function doCheck(array $key, $role)
    {
        $next = $this -> getData($role);
        
        foreach ($key as $part) {
            if (isset($next[$part])) {
                if (!$next[$part]['access']) {
                    return false;
                }
                
                $next = isset($next[$part]['items']) ? $next[$part]['items'] : [];
                
            } else {
                return isset($next['*']) ? $next['*']['access'] : false;
            }
        }
        
        return true;
    }
    
    /**
     * Set access for specified role and key.
     * 
     * @param string|Role
     * @param string|array
     * @param boolean
     */
    public function set($role, $key, $value)
    {
        $role = $this -> getRole($role);
        $value = (boolean) $value;
        
        if ($this -> check($key, $role) == $value) {
            return;
        }
        
        if (!is_array($key)) {
            $key = explode('/', $key);
        }
        
        // search key
        $last = null;
        $next = & $this -> getData($role);
        
        foreach ($key as $part) {
            if (!isset($next[$part]) || !$next[$part]['access']) {
                $next[$part] = [
                    'access' => true
                ];
            }
            
            $last = & $next[$part];
            
            if (!isset($last['items'])) {
                $last['items'] = [];
            }
            
            $next = & $last['items'];
        }
        
        $last['access'] = $value;
    }
    
    /**
     * Save settings for speciefied role.
     * 
     * @param string|Role
     */
    public function save($role)
    {
        if (!$this -> backend instanceof WritableBackend) {
            throw new \InvalidArgumentException(
                'Provided backend must be instance of Vero\ALC\WritableBackend for permanent change of settings.'
            );
        }
        
        $role = $this -> getRole($role);
        $this -> backend -> save($role, $this -> getData($role));
    }
    
    /**
     * Check, if role argument is valid and returm role ID.
     * 
     * @param scalar|Role
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getRole($role)
    {
        if (!$role instanceof Role && !is_scalar($role)) {
            throw new \InvalidArgumentException('Role must be instance of Vero\ALC\Role or scalar.');
        }
        
        if ($role instanceof Role) {
            return $role -> getRole();
        }
        
        return $role;
    }
    
    /**
     * Load and return data for specified role.
     * 
     * @param string
     * @return array
     */
    protected function & getData($role)
    {
        if (!isset($this -> data[$role])) {
            $this -> data[$role] = (array) $this -> backend -> get($role);
        }
        
        return $this -> data[$role];
    }
}
