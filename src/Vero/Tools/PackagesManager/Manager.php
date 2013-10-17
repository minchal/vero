<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\PackagesManager;

use Vero\Config\FileJson;
use Vero\Filesystem\Node;
use Vero\Filesystem\Directory;

/**
 * Simple Package Manager to install and uninstall CMS packages/modules.
 * 
 * Package metafile contains list of files and directories to (un)install.
 */
class Manager
{
    /** @var string Path to CMS main directory */
    protected $baseInstance;
    
    /** @var string Path to repository main directory */
    protected $baseRepository;
    
    protected $packages = [];
    
    /**
     * Set paths to repository and installation dir.
     * @param type $repo
     */
    public function __construct($repo, $instance)
    {
        $this -> baseInstance = $instance . DIRECTORY_SEPARATOR;
        
        $this -> loadRepository($repo);
    }
    
    /**
     * Install package.
     * 
     * Listener receives two arguments:
     *  - string $item: relative path to file/directory
     *  - int $status: 0 - ignored, 1 - installed, 2 - overwritten (when forced install)
     * 
     * @param string $package
     * @param boolean $force
     * @param callable $listener
     */
    public function install(Package $package, $force = false, callable $listener = null)
    {
        foreach ($package->getFiles() as $item) {
            $source = new Node($this -> baseRepository . $item);
            $dest   = new Node($this -> baseInstance . $item);
            
            $exists = $dest -> exists();
            
            if ($exists && $force) {
                $status = 2;
            } elseif (!$exists) {
                $status = 1;
            } else {
                $status = 0;
            }
            
            if ($status > 0) {
                if (!file_exists($dest->getPath())) {
                    Directory::ensure($dest->getPath());
                }
                
                $source -> getSpecialized() -> copy($dest);
            }
            
            if ($listener) {
                $listener($item, $status);
            }
        }
    }
    
    /**
     * Uninstall package.
     * 
     * Listener receives two arguments:
     *  - string $item: relative path to file/directory
     *  - int $status: 0 - file not found, 1 - deleted
     * 
     * @param string $package
     * @param boolean $force
     * @param callable $listener
     */
    public function uninstall(Package $package, callable $listener = null)
    {
        foreach ($package->getFiles() as $item) {
            $dest = new Node($this -> baseInstance . $item);
            $exists = $dest -> exists();
            
            if ($exists) {
                $dest -> getSpecialized() -> delete();
            }
            
            if ($listener) {
                $listener($item, $exists);
            }
        }
    }
    
    /**
     * Check isntallation status of package.
     * 
     * 2 - partially isntalled
     * 1 - isntalled
     * 0 - not found
     * 
     * @return int
     */
    public function checkStatus(Package $package, callable $listener = null)
    {
        $found = 0;
        
        foreach ($package->getFiles() as $item) {
            $exists = file_exists($this -> baseInstance . $item);
            
            if ($exists) {
                $found++;
            }
            
            if ($listener) {
                $listener($item, $exists);
            }
        }
        
        if ($found == count($package->getFiles())) {
            return 1;
        }
        
        if (!$found) {
            return 0;
        }
        
        return 2;
    }
    
    /**
     * Get files and directories paths from package, relative to CMS.
     * 
     * @param string $package
     * @return Package
     */
    public function getPackage($name)
    {
        if (!isset($this->packages[$name])) {
            $json = new FileJson($this->packagesDir . $name . '.json');
            
            $package = new Package($name);
            $package -> setFiles($json->get('files', []));
            $package -> setDeps($json->get('deps', []));
            
            $this->packages[$name] = $package;
        }
        
        return $this->packages[$name];
    }
    
    /**
     * Load configuration file from repository.
     * 
     * @param string $repo Path to repo
     */
    protected function loadRepository($repo)
    {
        $config = new FileJson($repo . DIRECTORY_SEPARATOR . 'config.json');
        
        $this -> baseRepository = realpath($repo . DIRECTORY_SEPARATOR . $config->get('base'))
                                     . DIRECTORY_SEPARATOR;
        
        $this -> packagesDir    = realpath($repo . DIRECTORY_SEPARATOR . $config->get('packages', 'packages'))
                                     . DIRECTORY_SEPARATOR;
    }
}
