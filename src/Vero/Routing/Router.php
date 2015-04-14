<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * Router.
 * Holds instances of Routes, allows to match request URL to one of 
 * Routes and create parametrized URLs to Routes.
 */
class Router
{
    /**
     * Array of registered routes.
     * Each key is required and static part of route pattern.
     * 
     * Example:
     *    '/news/' => ['/news/:id', '/news/:id/delete']
     *    '/page/' => ['/page/:url']
     * 
     * Each item is array of all matched routes.
     */
    public $routes = [];
    
    /**
     * Array of registered routes, but keys are uniqe names of each route.
     * 
     * Example:
     *    news/item => '/news/:id'
     *    news/delete => '/news/:id/delete'
     */
    protected $urls = [];
    
    /**
     * Instance of URL ready to clone and fill with action part.
     */
    protected $basicUrl;
    
    /**
     * Array of default params for URL's generation.
     */
    protected $defaultParams = [];
    
    /**
     * Construct instance of router.
     * Set default scheme, domain, basePath and prefix for created URLs.
     * 
     * @param string
     * @param string
     * @param string
     * @param string
     */
    public function __construct($base = '/', $prefix = '', $domain = null, $scheme = null)
    {
        $this -> defaultUrl = new URL();
        $this -> defaultUrl
            -> setScheme($scheme ? $scheme : 'http')
            -> setDomain($domain ? $domain : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'))
            -> setBase($base)
            -> setPrefix($prefix);
    }
    
    /**
     * Add param to default params array.
     * 
     * @param string
     * @param mixed
     * @return self
     */
    public function setDefaultParam($name, $value)
    {
        $this -> defaultParams[$name] = $value;
        return $this;
    }
    
    /**
     * Add params to default params array.
     * 
     * @return self
     */
    public function setDefaultParams(array $params)
    {
        foreach ($params as $name => $value) {
            $this -> setDefaultParam($name, $value);
        }
        
        return $this;
    }
    
    /**
     * Get current base URL.
     * 
     * @return string
     */
    public function getBase()
    {
        return $this -> defaultUrl -> getBase();
    }
    
    /**
     * Get current URL prefix.
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this -> defaultUrl -> getPrefix();
    }
    
    /**
     * Change default url used to generate action urls.
     */
    public function setDefaultUrl(URL $url)
    {
        $this -> defaultUrl = $url;
    }
    
    /**
     * Get all registered routes.
     * Keys in array are IDs.
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this -> urls;
    }
    
    /**
     * Register Route in this Router.
     * 
     * Method can be used in chain.
     * 
     * @param string Key at witch Route will be available in method url()
     * @retrun self
     */
    public function addRoute($id, Route $route)
    {
        $prefix = $route -> getPrefix();
        
        if (!isset($this -> routes[$prefix])) {
            $this -> routes[$prefix] = [];
        }
        
        $this -> routes[$prefix][] = [$route, $id];
        $this -> urls[$id] = $route;
        
        return $this;
    }
    
    /**
     * Get Route registered with speciefied key.
     * 
     * @param string
     * @return Route
     * @throws \OutOfBoundsException
     */
    public function getRoute($id)
    {
        if (!isset($this -> urls[$id])) {
            throw new \OutOfBoundsException(sprintf('Route with id "%s" not found!', $id));
        }
        
        return $this -> urls[$id];
    }
    
    /**
     * Match request URL to one of registered actions.
     * 
     * @param string URL of request to match
     * @param HTTP method of request
     * @return array|boolean Matched action ID, class and Arguments or false
     */
    public function match($request, $method = 'GET')
    {
        // search in routes with prefixes
        // only, when not empty (index page) request URL
        if ($request) {
            foreach ($this -> routes as $prefix => $routes) {
                // method match() can be slower than simple strpos()
                if ($prefix && strpos($request, $prefix) === 0) {
                    if ($ret = $this -> matchRoutes($routes, $request, $method)) {
                        return $ret;
                    }
                }
            }
        }
        
        // search in routes without prefix
        if (isset($this -> routes[''])) {
            return $this -> matchRoutes($this -> routes[''], $request, $method);
        }
        
        return false;
    }
    
    /**
     * Search matching route.
     * 
     * @param array $routes
     * @param string URL of request to match
     * @return array|boolean
     */
    protected function matchRoutes($routes, $request, $method)
    {
        $args = [];
        
        foreach ($routes as $r) {
            list($route, $id) = $r;

            if ($route -> match($request, $method, $args)) {
                $args = array_map('urldecode', $args);
                return array($id, $route -> getAction(), $args);
            }
        }
        
        return false;
    }
    
    /**
     * Get formated URL of speciefied ID with params.
     * 
     * Method can be used in 3 ways:
     *    url('id', array('arg1'=>'value1','arg3'=>'value3'))  (recommended)
     *    url('id', array('value1', null, 'value3'))
     *    url('id', 'value1', null, 'value3')
     * 
     * If $id is not speciefied default url with scheme, domain, base and prefix is returned.
     * 
     * @param string
     * @param array
     * @return URL
     * @throws \OutOfRangeException
     */
    public function url($id = null, $params = [])
    {
        if ($id === null) {
            return $this -> defaultUrl -> copy();
        }
        
        if (!isset($this -> urls[$id])) {
            throw new \OutOfRangeException('Route with ID "'.$id.'" is not registered in Router.');
        }
        
        $url = $this -> urls[$id];
        $available = $url -> getAvailableParams();
        $result = [];
        
        foreach ($available as $name) {
            if (isset($this -> defaultParams[$name])) {
                $result[$name] = $this -> defaultParams[$name];
            }
        }
        
        // if param is object, search for keys in this object
        if (is_object($params) && !method_exists($params, '__toString')) {
            foreach ($available as $name) {
                $v = $this -> tryToGetObjectProperty($params, $name);
                
                if ($v !== null) {
                    $result[$name] = $v;
                }
            }
            
        } else {
            if (!is_array($params)) {
                $params = func_get_args();
                array_shift($params); // remove $id
            }

            reset($params);

            // if first key is integer, others should be too
            if (is_int(key($params))) {
                $i = 0;
                
                foreach ($available as $name) {
                    if (!isset($result[$name])) {
                        if (isset($params[$i])) {
                            $result[$name] = $params[$i];
                        }
                        
                        $i++;
                    }
                }
                
            } else {
                foreach ($available as $name) {
                    if (isset($params[$name])) {
                        $result[$name] = $params[$name];
                    }
                }
            }
        }
        
        return $this -> defaultUrl -> copy() -> setAction(
            $this -> urls[$id] -> url($result)
        );
    }

    /**
     * Try to find property value in object.
     * 
     * @param object
     * @param string
     * @return mixed
     */
    protected function tryToGetObjectProperty($object, $property)
    {
        if (isset($object -> $property)) {
            return $object -> $property;
        }
        
        if (is_callable([$object, $property])) {
            return $object -> $property();
        }
        
        if (is_callable([$object, 'get'.$property])) {
            $m = 'get'.$property;
            return $object -> $m();
        }
        
        return null;
    }

    /**
     * Check, if route is registered in this router.
     * 
     * @param string
     * @return boolean
     */
    public function has($id)
    {
        return isset($this -> urls[$id]);
    }
}
