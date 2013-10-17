<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n\Backend;

use Vero\Cache\Cache;

/**
 * Cached Xliff files backend for I18n service.
 */
class Xliff extends Cached
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
        $strings = array();
        
        if (!file_exists($file)) {
            return $strings;
        }
        
        $xml = simplexml_load_file($file);
        $xml -> registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');
        
        foreach ($xml -> xpath('//xliff:trans-unit') as $tag) {
            $strings[(string) $tag->source] = (string) $tag->target;
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
        return $this->dir . "$lang/$section.xliff";
    }
}
