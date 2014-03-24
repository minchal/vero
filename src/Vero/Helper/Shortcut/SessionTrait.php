<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

/**
 * Shortcuts to use default session service.
 * 
 * Requires: DITrait, ResponseTrait
 */
trait SessionTrait
{
    /**
     * Add message remembered in session.
     * 
     * @param string
     * @param string
     * @return self
     */
    public function flash($msg, $type = 'success')
    {
        $this -> get('session') -> getBag('flash') -> set($type, $msg);
        return $this;
    }
    
    /**
     * Remember return URL in session and get short key.
     * 
     * Default: current request URL.
     * 
     * @param string|null|\Vero\Router\URL
     * @return self
     */
    public function addReturnUrl($url = null)
    {
        if (!$url) {
            $url = $this -> get('request') -> url;
        }
        
        $url = (string) $url;
        $key = substr(md5($url), 0, 10);
        
        $this -> get('session') -> getBag('returnUrls', ['max'=>20]) -> set($key, $url);
        
        return $key;
    }
    
    /**
     * Retrive remembered Return URL from key.
     * 
     * Default key: 'return' GET param.
     * 
     * @param string|null
     * @return self
     */
    public function returnUrl($default = null, $key = null)
    {
        if (!$key) {
            $key = $this -> get('request') -> get('return');
        }
        
        $session = $this -> get('session');
        
        if ($session -> hasBag('returnUrls') && $return = $session -> getBag('returnUrls') -> get($key)) {
            return $return;
        }
        
        return $default;
    }
    
    /**
     * Redirect to returnUrl or default URL.
     * 
     * @param \Vero\Router\URL
     * @return self
     */
    public function returnTo($default)
    {
        return $this -> redirect($this -> returnUrl($default));
    }
}
