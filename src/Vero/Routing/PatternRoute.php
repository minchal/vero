<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * Pattern Route allows to create Routes from simple patterns like:
 *    /news/{id}/delete
 * 
 * Pattern description:
 *    /static/part/{arg1:requirements:default}/more/{arg2}
 * 
 * Format of arguments:
 *   1. name of argument
 *   2. requirements (int, any)
 *   3. default value, if defined but empty, argument is optional
 */
class PatternRoute implements Route
{
    protected $action;
    
    protected $method;
    
    /**
     * Parameters list with default values
     *    name => default value
     */
    protected $params = [];
    
    protected $prefix;
    protected $regexp;
    protected $url;
    
    /**
     * Construct route with pattern, id and action speciefied.
     * If this class is used by Builder\XML with cache it can be serialized, 
     * so constructor is called only once, when loading route from XML file.
     * 
     * @param string $action Class name of action (with full namespace)
     * @param string $method
     * @param string $pattern
     * @param array $params
     * @see setPattern()
     */
    public function __construct($action, $method, $pattern, array $params = [])
    {
        $this -> action  = $action;
        $this -> method  = $method;
        $this -> setPattern($pattern, $params);
    }
    
    /**
     * Parse simple pattern and prepare: 
     *   prefix, match regexp and simple URL replace-ready form.
     * 
     * Optional params array can hold additional informations about each param:
     *  - default
     *  - required: boolean
     *  - reqs: regular exptesion part or one of words: int, any
     * 
     * @param string
     * @param array Array with additional informations about pattern parameters
     */
    public function setPattern($pattern, array $params = [])
    {
        $count = preg_match_all('/{(.*?)\}/', $pattern, $matches);
        
        // prefix:               pattern uses arguments       or    is static URL
        $prefix = $count ? strstr($pattern, $matches[0][0], true) : $pattern;
        
        // transform matched params to regexp and url
        $regexp = '@^'.$pattern.'$@';
        $url    = $pattern;
        
        foreach ($matches[0] as $i => $m) {
            $tmp = explode(':', $matches[1][$i]);
            $name = $tmp[0];
            
            if (in_array($name, ['query', 'action'])) {
                throw new \RuntimeException('Param name: "'.$name.'" is reserved (Action: '.$this->action.')!');
            }
            
            if (!isset($params[$name]['reqs'])) {
                $reqs = isset($tmp[1]) ? $tmp[1] : 'any';
            } else {
                $reqs = $params[$name]['reqs'];
            }
            
            if (!isset($params[$name]['default'])) {
                $default = isset($tmp[2]) ? $tmp[2] : null;
            } else {
                $default = $params[$name]['default'];
            }
            
            if (!isset($params[$name]['required'])) {
                $required = !isset($tmp[2]);
            } else {
                $required = $params[$name]['required'];
            }
            
            if (!$required) {
                $pos = strpos($regexp, $m);
                
                if (isset($regexp[$pos-1]) && $regexp[$pos-1]=='/') {
                    $regexp = substr_replace($regexp, '(?:/', $pos-1, 1);
                    $regexp = str_replace($m, $m.')?', $regexp);
                }
            }
            
            $regexp = str_replace($m, $this->toRegexp($name, $reqs, $required), $regexp);
            $url    = str_replace($m, '{'.$name.'}', $url);
            
            $this -> params[$name] = $default;
            
            if ($i==0 && !$required) {
                $prefix = (string) substr($prefix, null, -1);
            }
        }
        
        // set at end (or overwrite) default unmatched params values
        foreach ($params as $name => $item) {
            if (isset($item['default'])) {
                $this -> params[$name] = $item['default'];
            }
        }
        
        //x($pattern,$regexp,$url,'----');
        
        $this -> prefix = $prefix;
        $this -> regexp = $regexp;
        $this -> url    = $url;
    }
    
    /**
     * Transform string in format:
     *    name:int:default
     * to regular expresion fragment.
     * 
     * @param string
     * @param string 'int', 'any', 'idstr' or regular expresion part
     * @param boolean
     */
    protected function toRegexp($name, $reqs, $required)
    {
        // requirements
        if ($reqs == 'int') {
            $reqs = '\d+';
        } elseif ($reqs == 'idstr') {
            $reqs = '([a-zA-Z]+)([-_a-zA-Z0-9]*)';
        } elseif ($reqs == 'any') {
            $reqs = '[^/]+';
        }
        
        return '(?P<'.$name.'>'.$reqs.')' . ($required ? '' : '?');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return $this -> prefix;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this -> action;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAvailableParams()
    {
        return array_keys($this -> params);
    }
    
    /**
     * {@inheritdoc}
     */
    public function url($params = [])
    {
        $p = [];
        
        foreach ($this->params as $param => $default) {
            $p[$param] = isset($params[$param]) ? $params[$param] : '';
        }
        
        return new PatternActionPart($this -> url, $p);
    }
    
    /**
     * {@inheritdoc}
     */
    public function match($url, $method = 'GET', &$args = array())
    {
        if ($this -> method && $this -> method != $method) {
            return false;
        }
        
        if (!preg_match($this -> regexp, $url, $m)) {
            return false;
        }
        
        foreach ($this -> params as $param => $default) {
            $args[$param] = isset($m[$param]) ? $m[$param] : $default;
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this -> url;
    }
}
