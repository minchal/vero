<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n\Backend;

use Vero\I18n\Backend;
use Vero\Cache\Cache;

/**
 * Abstract I18n backend caching data.
 */
abstract class Cached implements Backend
{
    protected $cache;
    protected $data = array();
    
    /**
     * Create backend instance with Cache.
     * 
     * @param \Vero\Cache\Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this -> cache = $cache;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($lang, $section, $id)
    {
        if (!isset($this -> data[$lang][$section])) {
            $this -> data[$lang][$section] = $this -> load($lang, $section);
        }
        
        return isset($this -> data[$lang][$section][$id]) ? $this -> data[$lang][$section][$id] : null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAll($lang, $section)
    {
        if (!isset($this -> data[$lang][$section])) {
            $this -> data[$lang][$section] = $this -> load($lang, $section);
        }
        
        return $this -> data[$lang][$section];
    }
    
    /**
     * Try to load data from cache and if not exists, load from source.
     * 
     * @param string
     * @param string
     * @return array
     */
    protected function load($lang, $section)
    {
        $cacheId = 'i18n/'.$lang.'_'.$section;
        $data = $this -> cache -> fetch($cacheId);
        
        if (!$data || $data['saveTime'] < $this->getLastModTime($lang, $section)) {
            if (!$strings = $this -> loadFromSource($lang, $section)) {
                return array();
            }
            
            $data['data']     = $strings;
            $data['saveTime'] = time();
            
            $this -> cache -> save($cacheId, $data);
        }
        
        return $data['data'];
    }
    
    /**
     * Load section data from source.
     * If source does not exists this method should return false.
     * 
     * @return false|array
     */
    abstract protected function loadFromSource($lang, $section);
    
    /**
     * Get time of last modification of source.
     * If source is invalid, 
     * 
     * @return int
     */
    abstract protected function getLastModTime($lang, $section);
}
