<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\PackagesManager;

/**
 * Instance of package in Packages Manager.
 */
class Package
{
    protected $name;
    protected $files = [];
    
    protected $deps = [];
    
    /**
     * Create package with speciefied name.
     * 
     * @param string
     */
    public function __construct($name)
    {
        $this -> name = $name;
    }
    
    /**
     * Get package name.
     */
    public function getName()
    {
        return $this -> name;
    }
    
    /**
     * Set filss list in this package.
     * 
     * @param array
     */
    public function setFiles(array $files)
    {
        $this -> files = $files;
    }
    
    /**
     * Get files in this package.
     */
    public function getFiles()
    {
        return $this -> files;
    }
    
    /**
     * Set filss list in this package.
     * 
     * @param array
     */
    public function setDeps(array $deps)
    {
        $this -> deps = $deps;
    }
    
    /**
     * Get package names, that this package depends on.
     * 
     * @return array
     */
    public function getDeps()
    {
        return $this -> deps;
    }
}
