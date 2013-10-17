<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Action;

use Vero\DependencyInjection\Container;

/**
 * Web action with session.
 */
abstract class Session extends Basic
{
    /**
     * @var \Vero\Web\Session\Session
     */
    protected $session;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        
        $this -> session = $container -> get('session');
    }
    
    /**
     * Add message remembered in session.
     * 
     * @param string
     * @param string
     * @return self
     */
    public function flash($msg, $type = 'success')
    {
        $this -> session -> getBag('flash') -> set($type, $msg);
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
        
        $this -> session -> getBag('returnUrls', ['max'=>20]) -> set($key, $url);
        
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
        
        if ($this -> session -> hasBag('returnUrls') &&
            $return = $this -> session -> getBag('returnUrls') -> get($key)
        ) {
            return $return;
        }
        
        return $default;
    }
}
