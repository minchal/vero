<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Session;

use Vero\Web\Request;
use Vero\Web\Response;
use Vero\Helper\TokenGenerator;

/**
 * Session for HTTP request.
 * Session ID passed only by cookies.
 */
class Session
{
    /**
     * ID of current session.
     */
    protected $id;
    
    /**
     * Array of data in current session.
     */
    protected $data = array();
    
    /**
     * True if session was closed and can't change variables.
     */
    protected $closed = false;
    
    protected $backend;
    
    // configuration
    protected $cookie;
    protected $ttl;
    protected $refreshTime;
    protected $refreshVisits;
    protected $gcProbability;
    
    protected $request;
    
    /**
     * Create session on request and response.
     * 
     * Posible config options:
     *  - cookie: cookie name (default: 'ses')
     *  - ttl: seconds that session is valid (default: 86400s)
     *  - refreshTime (default: disabled)
     *  - refreshVisits (default: disabled)
     *  - gcProbability (from 0-never to 1-always, default: 0.1)
     * 
     * @param \Vero\Web\Session\Backend $backend
     * @param array $config
     */
    public function __construct(Backend $backend, array $config = [])
    {
        $config = array_merge(
            [
                'cookie'        => 'ses',
                'ttl'           => 86400,
                'refreshTime'   => 0,
                'refreshVisits' => 0,
                'gcProbability' => 0.1
            ],
            $config
        );
        
        $this -> backend       = $backend;
        $this -> cookie        = $config['cookie'];
        $this -> ttl           = $config['ttl'];
        $this -> refreshTime   = $config['refreshTime'];
        $this -> refreshVisits = $config['refreshVisits'];
        $this -> gcProbability = $config['gcProbability'];
    }
    
    /**
     * Try to load session or create new.
     */
    public function start(Request $request)
    {
        if (!$this -> load($request)) {
            $this -> create($request);
        }
        
        $this -> request = $request;
    }
    
    /**
     * Try to load session.
     * 
     * @return boolean true if session was successfully loaded
     */
    public function load(Request $request)
    {
        if (!($id = $request -> cookie($this->cookie))) {
            return false;
        }
        
        if (!($data = $this -> backend -> load($id, $this -> ttl)) ||
            $data['IP']        != $request -> ip()                 ||
            $data['UserAgent'] != $request -> userAgent()
        ) {
            return false;
        }
        
        $this -> id   = $id;
        $this -> data = $data;
        
        return true;
    }
    
    /**
     * Create new session.
     */
    public function create(Request $request)
    {
        $this -> id = TokenGenerator::get();
        
        $this -> data['IP']        = $request -> ip();
        $this -> data['UserAgent'] = $request -> userAgent();
        
        $this -> data['refreshTime']   = time();
        $this -> data['refreshVisits'] = 0;
    }
    
    /**
     * Destroy session.
     */
    public function destroy(Response $response)
    {
        $this -> backend -> delete($this -> id);
        $this -> id   = null;
        $this -> data = array();
        $response -> cookie($this -> cookie, '', 1);
    }
    
    /**
     * Close session and save data.
     */
    public function close(Response $response)
    {
        if ($this -> id) {
            if ($this -> isRefreshReady()) {
                $this -> backend -> delete($this -> id);
                $this -> id = TokenGenerator::get();
                
                $this -> data['refreshTime'] = time();
                $this -> data['refreshVisits'] = 0;
            }
            
            $this -> data['refreshVisits']++;
            $this -> data['lastQuery'] = $this -> request -> url();
            $this -> data['lastTime']  = time();
            
            $this -> backend -> save($this -> id, $this -> data, $this -> ttl);
            
            $response -> cookie($this -> cookie, $this -> id, time() + $this->ttl);
        }
        
        if (rand()/getrandmax() < $this->gcProbability) {
            $this -> backend -> clear($this -> ttl);
        }
        
        $this -> closed = true;
    }
    
    /**
     * Check, if session satisfies conditions to regenerate ID.
     * 
     * @return boolean
     */
    public function isRefreshReady()
    {
        if ($this -> refreshTime && $this -> data['refreshTime'] + $this -> refreshTime <= time()) {
            return true;
        }
        
        if ($this -> refreshVisits && $this -> data['refreshVisits'] > $this -> refreshVisits) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get session ID
     * 
     * @return string
     */
    public function getId()
    {
        return $this -> id;
    }
    
    /**
     * @see get()
     */
    public function __get($name)
    {
        return $this -> get($name);
    }
    
    /**
     * @see set()
     */
    public function __set($name, $value)
    {
        $this -> set($name, $value);
    }
    
    /**
     * Set session variable.
     * 
     * @param string $name
     * @param mixed $value
     * @return \Vero\Web\Session\Session
     */
    public function set($name, $value)
    {
        if ($this -> closed) {
            throw new \LogicException('Session was closed and data can not be changed!');
        }
        
        $this -> data[$name] = $value;
        
        return $this;
    }
    
    /**
     * Get variable from session.
     * 
     * @param string $name
     * @param mixed $defValue
     * @return mixed
     */
    public function get($name, $defValue = null)
    {
        if (isset($this -> data[$name])) {
            return $this -> data[$name];
        }
        
        return $defValue;
    }
    
    /**
     * Remove session variable.
     * 
     * @param string $name
     * @return mixed Value of deleted variable or $defValue
     */
    public function delete($name, $defValue = null)
    {
        if (isset($this -> data[$name])) {
            $r = $this -> data[$name];
            unset($this -> data[$name]);
            return $r;
        }
        return $defValue;
    }
    
    /**
     * Get or create Bag.
     * 
     * @param string
     * @param array
     * @return Bag
     */
    public function getBag($name, array $options = [])
    {
        $key = 'bag_'.$name;
        
        if (!isset($this->data[$key]) || !$this->data[$key] instanceof Bag) {
            $this -> data[$key] = new Bag($options);
        }
        
        return $this -> data[$key];
    }
    
    /**
     * Check, if session has specified Bag.
     * 
     * @param string
     * @return boolean
     */
    public function hasBag($name)
    {
        $key = 'bag_'.$name;
        return isset($this -> data[$key]) && $this->data[$key] instanceof Bag;
    }
}
