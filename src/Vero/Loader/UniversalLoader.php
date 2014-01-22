<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Loader;

/**
 * Class loader compatible with PSR-0.
 * 
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 */
class UniversalLoader
{
    const NS = '\\';
    
    protected $prefixes = [];
    protected $classMap = [];
    protected $extension;
    
    /**
     * @param string
     */
    public function __construct($extension = '.php')
    {
        $this -> extension = $extension;
    }
    
    /**
     * Register this loader as autoloader.
     * 
     * @param bool Whether to prepend the autoloader or not
     * @return UniversalLoader
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'load'), $prepend);
        return $this;
    }
    
    /**
     * Unregister this autoloader.
     * @return UniversalLoader
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load'));
        return $this;
    }
    
    /**
     * Get all registered namespaces.
     * 
     * @return array
     */
    public function getNamespaces()
    {
        $r = [];
        
        foreach ($this -> prefixes as $i) {
            $r[$i[0]] = $i[1];
        }
        
        return $r;
    }
    
    /**
     * @param array
     * @return UniversalLoader
     */
    public function addClassMap(array $map)
    {
        $this -> classMap = array_merge($this -> classMap, $map);
        return $this;
    }
    
    /**
     * Add recognized namespace or class prefix.
     * 
     * @param string
     * @param string
     * @return UniversalLoader
     */
    public function add($prefix, $dir = '')
    {
        $this -> prefixes[] = [$prefix, $dir, false];
        return $this;
    }
    
    /**
     * Add recognized namespace or class prefix.
     * Value od $prefix will be removed from path before including file.
     * 
     * @param string
     * @param string
     * @return UniversalLoader
     */
    public function addDirect($prefix, $dir)
    {
        $this -> prefixes[] = [$prefix, $dir, true];
        return $this;
    }
    
    /**
     * Add array of recognized namespaces or class prefixes.
     * 
     * @param array
     * @return UniversalLoader
     */
    public function addAll($items)
    {
        foreach ($items as $ns => $dirs) {
            foreach ((array) $dirs as $dir) {
                $this -> add($ns, $dir.'/');
            }
        }
        
        return $this;
    }
    
    /**
     * Load class file.
     * 
     * @param string Class name
     * @return boolean
     */
    public function load($class)
    {
        if ($file = $this -> findFile($class)) {
            require $file;
            return true;
        }
        
        return false;
    }
    
    /**
     * Find class file.
     * Metod is searching in registered prefixes.
     * 
     * @param string Class name
     * @return string|null
     */
    public function findFile($class)
    {
        $class = ltrim($class, self::NS);
        $namespace = '';
        $packageDir = '';
        
        if (isset($this -> classMap[$class])) {
            return $this -> classMap[$class];
        }
        
        foreach ($this -> prefixes as $item) {
            list($prefix, $prefixDir, $removePrefix) = $item;
            
            if ($prefix && strpos($class, $prefix) !== 0) {
                continue;
            }
            
            if ($removePrefix) {
                $class = substr($class, strlen($prefix)+1);
            }
            
            if ($lastNsPos = strripos($class, self::NS)) {
                $namespace = substr($class, 0, $lastNsPos);
                $class     = substr($class, $lastNsPos + 1);
                
                if ($namespace) {
                    $packageDir = str_replace(self::NS, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
                }
            }
            
            $file =
                $prefixDir . DIRECTORY_SEPARATOR . $packageDir .
                str_replace('_', DIRECTORY_SEPARATOR, $class) . $this -> extension;
            
            //var_dump($file);
            
            if (file_exists($file)) {
                return $file;
            }
        }
    }
}
