<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Action;

use Vero\DependencyInjection\Container;

/**
 * User web action.
 */
abstract class User extends Session
{
    /**
     * @var \Vero\Web\Auth\Manager
     */
    protected $auth;
    
    /**
     * @var \Vero\Web\Auth\User
     */
    protected $user;
    
    /**
     * @var \Vero\ACL\ACL
     */
    protected $acl;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        
        $this -> auth = $container -> get('auth');
        $this -> user = $this -> auth -> getUser();
        $this -> acl  = $container -> get('acl');
    }
    
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
        if (!$key) {
            $key = $this -> get('request') -> action;
        }
        
        if (!$this -> acl -> check($key)) {
            throw $this -> accessDenied($key);
        }
        
        return true;
    }
}
