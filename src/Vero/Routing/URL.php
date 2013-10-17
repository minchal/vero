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
    protected $scheme;
    protected $domain;
    protected $base;
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
     * @return \Vero\Routing\URL
     */
    public function copy()
    {
        return clone $this;
    }
    
    /**
     * Replace parameter in action part.
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
     * Show schame and domain part when toStringing.
     */
    public function full()
    {
        $this -> isFull = true;
        return $this;
    }
    
    /**
     * Hide schame and domain part when toStringing.
     */
    public function relative()
    {
        $this -> isFull = false;
        return $this;
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
     * @return self
     */
    public function setDomain($e)
    {
        $this -> domain = $e;
        return $this;
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
     * @return self
     */
    public function setPrefix($e)
    {
        $this -> prefix = $e;
        return $this;
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
