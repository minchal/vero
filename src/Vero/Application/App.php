<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Application;

use Vero\Config\Config;
use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\InstanceService;

/**
 * Abstract application.
 * Holds configuration and Dependency Injection Container.
 */
abstract class App
{
    protected $debug;
    protected $config;
    protected $container;
    protected $paths = array();
    
    /**
     * Create instance of application.
     */
    public function __construct(Config $config)
    {
        $this -> config    = $config;
        $this -> debug     = (boolean) $this -> config -> get('debug');
        $this -> container = new Container();
        
        $this -> prepareEnvironment();
        $this -> prepareContainer();
    }
    
    /**
     * Prepare environment for application.
     */
    protected function prepareEnvironment()
    {
        if ($this -> debug) {
            error_reporting(-1);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        mb_internal_encoding('UTF-8');
        
        if ($locale = $this -> config -> get('locale')) {
            setlocale(LC_ALL, $locale);
        }
        
        date_default_timezone_set($this -> config -> get('timezone', 'Europe/London'));
    }
    
    /**
     * Prepare Dependency Injection Container for this application.
     */
    protected function prepareContainer()
    {
        $this -> container
            -> set('app', new InstanceService($this))
            -> set('config', new InstanceService($this -> config));
    }
    
    /**
     * Get path to directory or file associated with application.
     */
    public function path($path)
    {
        if (($prefix = strstr($path, '/', true)) === false) {
            $prefix = $path;
        }
        
        if (!isset($this -> paths[$prefix])) {
            throw new \OutOfRangeException('Specified path prefix "'.$prefix.'" was not recognized!');
        }
        
        return $this -> paths[$prefix] . substr($path, strlen($prefix)+1);
    }
    
    /**
     * Register path for method path().
     */
    public function registerPath($prefix, $path)
    {
        if (file_exists($path)) {
            $path = realpath($path);
        }
        
        $this -> paths[$prefix] = $path.'/';
        return $this;
    }
    
    /**
     * Is application in debug mode.
     * 
     * @return boolean
     */
    public function debug()
    {
        return $this -> debug;
    }
    
    /**
     * Return signature of application.
     * 
     * @return string
     */
    public function signature()
    {
        return 'Vero: ' . \Vero\Version::VERSION;
    }
}
