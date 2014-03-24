<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Helper class for loading Services from PHP files.
 */
class MapLoader
{
    /** @var Container */
    private $container;
    
    /**
     * Create loader for speciefied Container.
     */
    public function __construct(Container $container)
    {
        $this -> container = $container;
    }
    
    /**
     * Load all files in directory
     * 
     * @param string
     * @return self
     */
    public function loadAll($dir)
    {
        foreach (glob($dir.'/*.php') as $file) {
            $this -> load($file);
        }
        
        return $this;
    }
    
    /**
     * Load services from PHP file.
     * 
     * @param string
     * @return self
     */
    public function load($file)
    {
        $items = (array) include $file;

        foreach ($items as $id => $service) {
            $this -> container -> set($id, $service);
        }
        
        return $this;
    }
}
