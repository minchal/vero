<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * URL instance.
 */
class URL
{
    protected $scheme = 'http';
    protected $domain;
    protected $base = '/';
    protected $prefix;
    protected $action;
    protected $get = [];
    protected $anchor;
    
    protected $isFull = false;
    
    public function __construct()
    {
        
    }
    
    /**
     * Every copy of URL with action object must have own 
     * parameters list for replace() method.
     */
    public function __clone()
    {
        if (is_object($this -> action)) {
            $this -> action = clone $this -> action;
        }
    }
    
    /**
     * Return clone of instance.
     * 
     * @return self
     */
    public function copy()
    {
        return clone $this;
    }
    
    /**
     * Replace parameter in action part.
     * @return self
     */
    public function replace($from, $to)
    {
        if ($this -> action instanceof ActionPart) {
            $this -> action -> replace($from, $to);
        } else {
            $this -> action = str_replace($from, $to, $this -> action);
        }
        
        return $this;
    }
    
    /**
     * Show scheme and domain part when toStringing.
     * 
     * @return self
     */
    public function full()
    {
        $this -> isFull = true;
        return $this;
    }
    
    /**
     * Hide scheme and domain part when toStringing.
     * 
     * @return self
     */
    public function relative()
    {
        $this -> isFull = false;
        return $this;
    }
    
    /**
     * Clear base and prefix parts.
     * 
     * @return self
     */
    public function clearBase()
    {
        return $this -> setBase('') -> setPrefix('');
    }
    
    /**
     * @return self
     */
    public function setScheme($e)
    {
        $this -> scheme = $e;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getScheme()
    {
        return $this -> scheme;
    }
    
    /**
     * @return self
     */
    public function setDomain($e)
    {
        $this -> domain = $e;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDomain()
    {
        return $this -> domain;
    }
    
    /**
     * @return self
     */
    public function setBase($e)
    {
        $this -> base = $e;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getBase()
    {
        return $this -> base;
    }
    
    /**
     * @return self
     */
    public function setPrefix($e)
    {
        $this -> prefix = $e;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this -> prefix;
    }
    
    /**
     * @return self
     */
    public function setAction($e)
    {
        $this -> action = $e;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAction()
    {
        return $this -> action;
    }
    
    /**
     * @return self
     */
    public function setGet($k, $v = null)
    {
        if (is_array($k)) {
            $this -> get = $k;
        } else {
            $this -> get[$k] = $v;
        }
        
        return $this;
    }
    
    /**
     * @return string|array
     */
    public function getGet()
    {
        return $this -> get;
    }
    
    /**
     * Set anything as GET part.
     * 
     * @return self
     */
    public function setGetRaw($e)
    {
        $this -> get = $e;
        return $this;
    }
    
    /**
     * Add/merge array for GET query part.
     * 
     * @return self
     */
    public function addGet(array $e)
    {
        $this -> get = array_merge($this -> get, $e);
        return $this;
    }
    
    /**
     * @return self
     */
    public function setAnchor($e)
    {
        $this -> anchor = $e;
        return $this;
    }
    
    /**
     * @return string|array
     */
    public function getAnchor()
    {
        return $this -> anchor;
    }
    
    /**
     * Return instance as string.
     * 
     * @return string
     */
    public function asString()
    {
        $r = '';
        
        if ($this -> isFull) {
            $r .= $this->scheme . '://' . $this->domain;
        }
        
        $r .= $this->base;
        
        // avoiding 2-times __toString() call
        $action = (string) $this -> action;
        
        if ($action) {
            $r .= $this->prefix . $action;
        }
        
        if ($this -> get) {
            if (is_array($this -> get)) {
                $r .= '?'.http_build_query($this -> get, null, '&');
            } else {
                $r .= '?'.ltrim($this -> get, '?');
            }
        }
        
        if ($this -> anchor) {
            $r .= '#'.ltrim($this -> anchor, '#');
        }
        
        return $r;
    }
    
    /**
     * @see asString()
     */
    public function __toString()
    {
        return $this -> asString();
    }
}
