<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

/**
 * Shortcuts to use default translator service.
 * 
 * Requires: DITrait
 */
trait I18nTrait
{
    /**
     * Set domain for Translator.
     * If no domain is speciefied, get domain from routing module name.
     * 
     * @param string
     * @return Vero\I18n\Translator
     */
    public function setDomain($domain = null)
    {
        if (!$domain) {
            list($domain) = explode('/', $this -> get('request') -> action);
        }
        
        return $this -> get('i18n') -> setDomain($domain);
    }
    
    /**
     * Shortcut to call get() method on Translate object.
     * 
     * @return string
     */
    public function i18n($id, $params = [], $domain = null)
    {
        return $this -> get('i18n') -> get($id, $params, $domain);
    }
    
    /**
     * Shortcut to call getGlobal() method on Translate object.
     * 
     * @return string
     */
    public function i18ng($id, $params = [])
    {
        return $this -> get('i18n') -> getGlobal($id, $params);
    }
}
