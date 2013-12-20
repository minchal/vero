<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

/**
 * Class represents one complex HTTP request.
 * It works with global arrays: GET, POST, COOKIE, FILES, SERVER
 * 
 * To create query string based on $_SERVER['REQUEST_URI'] specify 
 * basepatch (directory) and prefix, example:
 *  base:    /vero/
 *  prefix:  index.php/
 * 
 * To parse query like:
 *  /vero/index.php/news/10/delete?get=arg1
 * 
 * to form ready for Router:
 *  /news/10/delete
 * 
 * Object can be used as array (ArrayAccess) but read-only.
 * In that case used array (POST/GET) depends on request method.
 */
class Request implements \ArrayAccess
{
    const GET  = 'GET';
    const POST = 'POST';
    
    const FILE_TEXT  = 1;
    const FILE_IMAGE = 2;
    
    /**
     * Named params for action and other reserved variables:
     *  - query
     *  - action
     */
    protected $params = [];
    
    /**
     * Request data
     */
    protected $globals = [];
    
    /**
     * Create request instance from globals arrays.
     */
    public static function createFromGlobals()
    {
        $request = new self([
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            'files' => $_FILES,
            'server' => $_SERVER,
        ]);
        
        return $request;
    }
    
    /**
     * Create request instance with any request data.
     */
    public function __construct(array $globals)
    {
        $this -> globals = $globals;
    }
    
    /**
     * Prepare query string, without base path, prefix and GET params.
     * 
     * If prefix ends with slash, two forms will be accepted:
     *   /base/prefix.php/
     *   /base/prefix.php
     * 
     * @param string
     * @param string
     * @return string
     */
    public function getQuery($base = '', $prefix = '')
    {
        $query = $this -> url();
        
        if ($base && strpos($query, $base) === 0) {
            $query = (string) substr($query, strlen($base));
        }
        if ($prefix && (strpos($query, $prefix) === 0 || strpos($query, rtrim($prefix, '/')) === 0)) {
            $query = (string) substr($query, strlen($prefix));
        }
        
        if (strpos($query, '?') !== false) {
            $query = (string) strstr($query, '?', true);
        }
        
        return $query;
    }
    
    /**
     * Set or overwrite existing params.
     * 
     * @param array $params
     * @return self
     */
    public function setParams(array $params)
    {
        $this -> params = array_merge($this -> params, $params);
        return $this;
    }
    
    /**
     * Set or overwrite existing param.
     * 
     * @param string $key
     * @param mixed  $param
     * @return self
     */
    public function setParam($key, $param)
    {
        $this -> params[$key] = $param;
        return $this;
    }
    
    /**
     * Shorthand for param() method.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this -> param($name);
    }
    
    /**
     * Check if param is set.
     * 
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this -> params[$name]);
    }
    
    /**
     * Get param from action query.
     * 
     * @return mixed
     */
    public function param($name = null, $defValue = null)
    {
        if ($name === null) {
            return $this -> params;
        }
        
        return isset($this -> params[$name]) && $this -> params[$name] ?
            $this -> params[$name] : $defValue;
    }
    
    /**
     * Get value from global GET array or entire array.
     * 
     * @see getGlobal()
     * @return mixed
     */
    public function get($name = null, $defValue = null)
    {
        return $this -> getGlobal('get', $name, $defValue);
    }
    
    /**
     * Get value from global POST array or entire array.
     * 
     * @see getGlobal()
     * @return mixed
     */
    public function post($name = null, $defValue = null)
    {
        return $this -> getGlobal('post', $name, $defValue);
    }
    
    /**
     * Get value from cookie.
     * 
     * @see getGlobal()
     * @return mixed
     */
    public function cookie($name = null, $defValue = null)
    {
        return $this -> getGlobal('cookie', $name, $defValue);
    }
    
    /**
     * Get value from SERVER array or default value, if variable is not set.
     * 
     * @return mixed
     */
    public function server($name, $defValue = null)
    {
        return $this -> getGlobal('server', $name, $defValue);
    }
    
    /**
     * Try to get uploaded file.
     * Method throws exceptions if uploaded file is invalid.
     * 
     * @param string $name FILES array index
     * @return \Vero\Web\UploadedFile
     * @throws \Vero\Web\Exception\Upload
     */
    public function file($name)
    {
        // @TODO: add support for files in array
        if (!isset($_FILES[$name]) || is_array($_FILES[$name]['tmp_name'])) {
            throw new Exception\Upload('upload error required', 'global');
        }
        
        return new UploadedFile($_FILES[$name]['tmp_name'], $_FILES[$name]['name'], $_FILES[$name]['error']);
    }
    
    /**
     * Get host of this request.
     * 
     * @return string
     */
    public function host()
    {
        return $this -> server('SERVER_NAME', 'localhost');
    }
    
    /**
     * Get current request URI.
     * Ex.: /vero/index.php/user/foo?p=bar
     * 
     * @return string
     */
    public function url()
    {
        return $this -> server('REQUEST_URI');
    }
    
    /**
     * Check if request is made by HTTPS.
     * 
     * @return boolean
     */
    public function isSecure()
    {
        $header = $this->server('HTTPS');
        return $header && $header != 'off';
    }
    
    /**
     * Get current request schame.
     * 
     * @return string
     */
    public function scheme()
    {
        return $this -> isSecure() ? 'https' : 'http';
    }
    
    /**
     * Get request method.
     * 
     * @return string
     */
    public function method()
    {
        return strtoupper($this -> server('REQUEST_METHOD', 'GET'));
    }
    
    /**
     * @return boolean
     */
    public function isPost()
    {
        return $this -> method() == self::POST;
    }
    
    /**
     * @return boolean
     */
    public function isGet()
    {
        return $this -> method() == self::GET;
    }
    
    /**
     * Return true, if request is made by ajax library.
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return $this -> server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
    }
    
    /**
     * Get request IP number.
     * 
     * @return string
     */
    public function ip()
    {
        return $this -> server('REMOTE_ADDR', '127.0.0.1');
    }
    
    /**
     * Get USER_AGENT header value.
     * 
     * @return string
     */
    public function userAgent()
    {
        return $this -> server('HTTP_USER_AGENT');
    }
    
    /**
     * Try to guess base URL for application.
     * 
     * @return string
     */
    public function guessBase()
    {
		return rtrim(dirname($this -> server('SCRIPT_NAME')), '/') . '/';
    }
    
    /**
     * Get Accept-Language as array.
     * This method returns only languages (without full locale).
     * 
     * @return array
     */
    public function acceptedLanguages()
    {
        $langs = array();
        
        foreach (explode(',', $this -> server('HTTP_ACCEPT_LANGUAGE')) as $i) {
            $parts = explode(';', $i);
            
            if (isset($parts[1]) && preg_match('/q=(1|0\.[0-9]+)/', $parts[1], $f)) {
                $q = (float) $f[1];
            } else {
                $q = 1;
            }
            
            list($lang) = explode('-', $parts[0]);
            
            if (!isset($langs[$lang])) {
                $langs[$lang] = $q;
            }
        }
        
        return $langs;
    }
    
    /**
     * Get part of GET or POST array, but only with not empty values.
     * 
     * @param array $keys
     * @param string $type
     * @return array
     */
    public function rewrite(array $keys, $type = self::GET)
    {
        $ret = [];
        
        foreach ($keys as $k) {
            if ($v = $this -> {$type}($k)) {
                $ret[$k] = $v;
            }
        }
        
        return $ret;
    }
    
    /**
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Changes of Request object are not allowed!');
    }
    
    /**
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Changes of Request object are not allowed!');
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if ($this -> isPost()) {
            return $this->post($offset) !== null;
        }
        return $this->get($offset) !== null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ($this -> isPost()) {
            return $this->post($offset);
        }
        return $this->get($offset);
    }
    
    /**
     * Helper to get data from global array (GET, POST, COOKIE).
     * 
     * @return mixed
     */
    protected function getGlobal($array, $name = null, $defValue = null)
    {
        if (!isset($this -> globals[$array])) {
            return $defValue;
        }
        
        if ($name === null) {
            return $this -> globals[$array];
        }
        
        if (!isset($this -> globals[$array][$name])) {
            return $defValue;
        }
        
        return $this -> globals[$array][$name];
    }
}
