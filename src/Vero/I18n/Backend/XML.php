<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n\Backend;

use Vero\Cache\Cache;

/**
 * Cached XML files backend for I18n service.
 */
class XML extends Cached
{
    protected $dir;
    
    /**
     * {@inheritdoc}
     * 
     * @param string
     */
    public function __construct(Cache $cache, $dir)
    {
        $this -> dir   = $dir;
        parent::__construct($cache);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function loadFromSource($lang, $section)
    {
        $file = $this -> getFileName($lang, $section);
        
        $xml = simplexml_load_file($file);
        $strings = array();
        
        foreach ($xml->children() as $tag) {
            $strings[(string) $tag -> attributes() -> id] = (string) $tag;
        }
        
        return $strings;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getLastModTime($lang, $section)
    {
        $file = $this -> getFileName($lang, $section);
        
        if (!file_exists($file)) {
            return 0;
        }
        
        return filemtime($file);
    }
    
    private function getFileName($lang, $section)
    {
        return $this->dir . "$lang/$section.xml";
    }
}
