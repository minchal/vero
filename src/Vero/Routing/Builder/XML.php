<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing\Builder;

use Vero\Routing\Router;
use Vero\Routing\PatternRoute;
use Vero\Cache\Cache;

/**
 * Builder from XML files directory.
 * Data is cached.
 */
class XML
{
    protected $dir;
    protected $cache;
    protected $ignoreModifications;
    
    /**
     * Construct builder for speciefied directory with cache instance.
     * 
     * @param string
     * @param Cache
     * @param boolean
     * @see getMtime()
     */
    public function __construct($dir, Cache $cache, $ignoreModifications = false)
    {
        $this -> dir   = $dir;
        $this -> cache = $cache;
        $this -> ignoreModifications = $ignoreModifications;
    }
    
    /**
     * Fill Router with all founded Routes.
     * This method will try first to search routes in cache.
     * 
     * @return Router
     */
    public function fill(Router $router)
    {
        $lastMod = $this -> getMtime();
        $data = $this -> cache -> fetch('routes');
        
        if (!$data || $data['saveTime'] < $lastMod) {
            $data['saveTime'] = time();
            $data['routes'] = $this -> buildRoutes();
            
            $this -> cache -> save('routes', $data);
        }
        
        foreach ($data['routes'] as $id => $route) {
            $router -> addRoute($id, $route);
        }
        
        return $router;
    }
    
    /**
     * Build Routes from XML files in directory.
     * 
     * @return array
     */
    protected function buildRoutes()
    {
        $routes = array();
        
        foreach (glob($this->dir.'*.xml') as $file) {
            $xml = simplexml_load_file($file);
            
            $attr = $xml -> attributes();
            $actionsNS = isset($attr['namespace']) ? $attr['namespace'].'\\'  : '';
            $module    = isset($attr['module'])    ? $attr['module'].'/'      : '';
            
            foreach ($xml->children() as $tag) {
                $id      = (string) $tag['id'];
                $pattern = (string) $tag -> url;
                $action  = (string) $tag -> action;
                
                $params = [];
                
                if (isset($tag -> params)) {
                    foreach ($tag -> params -> children() as $param) {
                        $arr = (array) $param -> attributes();
                        $arr = $arr['@attributes'];
                        
                        if (isset($arr['required'])) {
                            $arr['required'] = $arr['required'] == 'true' ? true : false;
                        }
                        
                        $params[$arr['name']] = $arr;
                    }
                }
                
                if (isset($routes[$module.$id])) {
                    throw new \RuntimeException('Duplicated Route ID: '.$module.$id.' in '.$file.'!');
                }
                
                $routes[$module.$id] = new PatternRoute(
                    $this->getActionClass($action, $actionsNS, $id),
                    $pattern,
                    $params
                );
            }
        }
        
        return $routes;
    }
    
    /**
     * Create action class name based on module namespace, action ID
     * and optional class name from XML file.
     * 
     * @param string $action
     * @param string $ns
     * @param string $id
     * @return string
     */
    protected function getActionClass($action, $ns, $id)
    {
        if (!$action) {
            $parts = explode('/', $id);
            
            foreach ($parts as &$part) {
                $part = implode('', array_map('ucfirst', explode('-', $part)));
            }
            
            $action = implode('\\', $parts);
        }
        
        return $ns . $action;
    }
    
    /**
     * Get last modification time of XML files in directory.
     * 
     * If ignoreModifications==true (e.g. in production env) returns 0 
     * (always smaller than last cache save).
     * 
     * @param string
     * @return int
     */
    protected function getMtime()
    {
        if ($this -> ignoreModifications) {
            return 0;
        }
        
        $max = 0;
        
        foreach (glob($this -> dir.'*.xml') as $file) {
            $max = max($max, filemtime($file));
        }
        
        return $max;
    }
}
