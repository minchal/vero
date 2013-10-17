<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Auth;

use Vero\Web\Session\Session;
use Vero\Web\Request;
use Vero\Web\Response;
use Vero\Helper\TokenGenerator;

/**
 * Class to manage authorization in web application (using session) 
 * and autologin mechanism.
 */
class Manager
{
    const HOUR  = 3600;     // 60*60
    const DAY   = 86400;    // 24*60*60
    const MONTH = 2678400;  // 31*24*60*60
    const YEAR  = 31536000; // 365*24*60*60
    
    /**
     * @var Session
     */
    protected $session;
    
    /**
     * @var UserProvider
     */
    protected $provider;
    
    /**
     * @var AutologinProvider
     */
    protected $autologin;
    
    protected $autologinCookie = 'autologin';
    
    protected $autologinTtl = self::YEAR;
    
    /**
     * @var User
     */
    protected $user;
    
    protected $userTtl = self::HOUR;
    
    protected $loaded = false;
    
    protected $loadListeners = [];
    
    /**
     * Create auth manager with session.
     * 
     * If autologin provider will be not speciefied, 
     * use of autologin(), remember() and forget() methods will be not posible.
     * 
     * @param Session
     * @param UserProvider
     * @param AutologinProvider|null $autologinProvider
     */
    public function __construct(Session $session, UserProvider $provider, AutologinProvider $autologin = null)
    {
        $this -> session   = $session;
        $this -> provider  = $provider;
        $this -> autologin = $autologin;
    }
    
    /**
     * Set session user "Time To Live" in seconds.
     * 
     * @param int
     * @return self
     */
    public function setUserTtl($ttl)
    {
        $this -> userTtl = $ttl;
        return $this;
    }
    
    /**
     * Set autologin "Time To Live" in seconds.
     * 
     * @see self::YEAR
     * @see self::MONTH
     * @param int
     * @return self
     */
    public function setAutologinTtl($ttl)
    {
        $this -> autologinTtl = $ttl;
        return $this;
    }
    
    /**
     * Set autologin cookie name.
     * 
     * @param string
     * @return self
     */
    public function setAutologinCookie($cookie)
    {
        $this -> autologinCookie = $cookie;
        return $this;
    }
    
    /**
     * Check, if this Manager provides autologin functionality.
     * 
     * @return boolean
     */
    public function usesAutologin()
    {
        return $this -> autologin instanceof AutologinProvider;
    }
    
    /**
     * Receive info about user visit.
     * 
     * @return self
     */
    public function visit(Request $request)
    {
        if ($this -> isLoggedIn()) {
            $this -> provider ->registerVisit($this->user->getId(), new \DateTime(), $request->url(), $request->ip());
        }
        
        return $this;
    }
    
    /**
     * Return true, if current session has authorized User.
     * 
     * @return boolean
     */
    public function isLoggedIn()
    {
        $this -> loadUser();
        return $this -> user !== null;
    }
    
    /**
     * Get current user.
     * This method allways returns instance of User, 
     * but that User can be unauthorized (Guest).
     * 
     * @return User
     */
    public function getUser()
    {
        $this -> loadUser();
        return $this -> user ? $this -> user : $this -> provider -> getDefaultUser();
    }
    
    /**
     * Set/change authorized user.
     * 
     * @return self
     */
    public function login(User $user)
    {
        $this -> user = $user;
        $this -> session -> user = $user -> getId();
        $this -> loaded = true;
        return $this;
    }
    
    /**
     * Logout currently authorized user.
     * 
     * @return self
     */
    public function logout()
    {
        $this -> user = null;
        $this -> session -> user = null;
        $this -> loaded = true;
        return $this;
    }
    
    /**
     * Try to autologin user:
     *  - search key in cookies
     *  - search key by provider
     *  - set authorized user
     * 
     * @return boolean True, if user was succesfully logged in
     */
    public function autologin(Request $request)
    {
        if (!$this -> autologin) {
            throw new \BadMethodCallException('To use AutoLogin functionality provide instance of AutologinProvider.');
        }
        
        $key = $request -> cookie($this -> autologinCookie);
        
        if (!$userId = $this -> autologin -> searchAutologinKey($key)) {
            return false;
        }
        
        if (!$user = $this -> provider -> getUser($userId)) {
            $this -> autologin -> removeAutologinKey($key);
            return false;
        }
        
        $this -> login($user);
        
        return true;
    }
    
    /**
     * Register autologed session for currently authorized user.
     * 
     * @return boolean True, if session will be remembered.
     */
    public function remember(Response $response)
    {
        if (!$this -> autologin) {
            throw new \BadMethodCallException('To use AutoLogin functionality provide instance of AutologinProvider.');
        }
        
        if (!$this -> user) {
            return false;
        }
        
        $key = TokenGenerator::get();
        
        $this -> autologin -> addAutologinKey($key, $this->user->getId());
        $response -> cookie($this -> autologinCookie, $key);
        
        return true;
    }
    
    /**
     * Register autologed session for currently authorized user.
     * 
     * @return self
     */
    public function forget(Request $request, Response $response)
    {
        if (!$this -> autologin) {
            throw new \BadMethodCallException('To use AutoLogin functionality provide instance of AutologinProvider.');
        }
        
        if ($key = $request -> cookie($this -> autologinCookie)) {
            $response -> cookie($this -> autologinCookie, '', 1);
            $this -> autologin -> removeAutologinKey($key);
        }
        
        return $this;
    }
    
    /**
     * Add listener, that waits for first user loading.
     */
    public function addLoadListener($i)
    {
        $this -> loadListeners[] = $i;
    }
    
    /**
     * Try to load user, if there was no attempt earlier.
     */
    protected function loadUser()
    {
        if ($this -> loaded) {
            return;
        }
        
        if ($this -> session -> lastTime < time()-$this->userTtl) {
            $this -> session -> user = null;
        }

        if ($this -> session -> user) {
            $this -> user = $this -> provider -> getUser($this -> session -> user);
        }

        $this -> loaded = true;
        
        foreach ($this -> loadListeners as $f) {
            $f($this -> getUser());
        }
    }
}
