<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper;

/**
 * 
 */
class InstancesConstructor
{
    /**
     * Create instances of all classes defined in specified directory.
     * 
     * @param string
     * @return array
     */
    public static function create($dir, $ns, callable $orderCallback = null)
    {
        $instances = [];
        
        foreach (glob($dir.'*.php') as $file) {
            $c = $ns . basename($file, '.php');
            $instances[] = new $c();
        }
        
        if ($orderCallback) {
            usort($instances, $orderCallback);
        }
        
        return $instances;
    }
}
