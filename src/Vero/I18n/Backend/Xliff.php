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
    const MODE_ID = 0;
    const MODE_SOURCE = 1;
    
    protected $dir;
    protected $extension = 'xlf';
    protected $mode = self::MODE_ID;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Cache $cache, $dir)
    {
        $this -> dir = $dir;
        parent::__construct($cache);
    }
    
    /**
     * Set files extension.
     * 
     * @param string
     * @return self
     */
    public function setExtension($extension)
    {
        $this -> extension = $extension;
        return $this;
    }
    
    /**
     * Get current files extension.
     * 
     * @return string
     */
    public function getExtension()
    {
        return $this -> extension;
    }
    
    /**
     * Get current ID mode.
     * 
     * @return int
     */
    public function getMode()
    {
        return $this -> mode;
    }
    
    /**
     * Set ID mode, one of:
     *  - source string as string key
     *  - trans-unit ID as string key
     * 
     * @param int
     * @return self
     */
    public function setMode($mode)
    {
        $this -> mode = $mode;
        return $this;
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
        
        $fileAttr = $xml -> attributes();
        
        $key = $this -> getKeyCallback();
        $str = $this -> getStringCallback($fileAttr -> {'source-language'}, $fileAttr -> {'target-language'});
        
        foreach ($xml -> xpath('//xliff:trans-unit') as $tag) {
            $strings[$key($tag)] = $str($tag);
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
        return $this -> dir . $lang . '/' . $section . '.' . $this -> extension;
    }
    
    private function getKeyCallback()
    {
        if ($this -> mode == self::MODE_ID) {
            return function($tag) {
                return (string) $tag -> attributes() -> id;
            };
        }
        
        return function($tag) {
            return (string) $tag -> source;
        };
    }
    
    private function getStringCallback($sourceLang, $targetLang)
    {
        /**
         * In IDMode allow to store string in source tag without translation.
         */
        if ($this -> mode == self::MODE_ID && $sourceLang == $targetLang) {
            return function($tag) {
                return (string) $tag -> target ? : (string) $tag -> source;
            };
        }
        
        return function($tag) {
            return (string) $tag -> target ? : null;
        };
    }
}
