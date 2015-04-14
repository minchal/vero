<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

/**
 * I18n service provides functions to translate (and formatting) string
 * and formating date, time with current locale.
 * 
 * @see \MessageFormatter
 */
class Translator
{
    /**
     * Default language (as fallback for not found strings)
     */
    protected $defaultLang;
    
    /**
     * Current user language.
     */
    protected $lang;
    
    /**
     * Current user locale.
     */
    protected $locale;
    
    /**
     * List of accepted languages and locales for each language.
     */
    protected $accepted = [];
    
    protected $backend;
    
    protected $domain = 'global';
    protected $domainStack = [];
    
    /**
     * Create service with speciefied default language and backend.
     * 
     * $accepted array must be indexed with language code and 
     * with locale string as value. Example:
     *  pl => pl_PL
     *  en => en_US
     * 
     * @param \Vero\I18n\Backend
     * @param string
     * @param array List of languages posible to select, if empty, all languages will be accepted
     * 
     * @see setLang()
     * @see \Vero\I18n\Backend
     */
    public function __construct(Backend $backend, $defaultLang, array $accepted = [], Formatter $formatter = null)
    {
        if (!$formatter) {
            $formatter = new IntlFormatter();
        }
        
        $this -> backend     = $backend;
        $this -> defaultLang = $defaultLang;
        $this -> accepted    = $accepted;
        $this -> formatter   = $formatter;
        
        $this -> setLang($defaultLang);
    }
    
    /**
     * Get current Backend instance.
     * 
     * @return Formatter
     */
    public function getBackend()
    {
        return $this -> backend;
    }
    
    /**
     * Get current Formatter instance.
     * 
     * @return Formatter
     */
    public function getFormatter()
    {
        return $this -> formatter;
    }
    
    /**
     * Get accepted locales array.
     * 
     * @return array
     */
    public function getAcceptedLocales()
    {
        return $this -> accepted;
    }
    
    /**
     * Set current language and locale.
     * Locale is choosen from accepted languages list.
     * 
     * @param string
     * @return self
     */
    public function setLang($lang)
    {
        if ($this -> accepted) {
            if (!isset($this -> accepted[$lang])) {
                throw new \OutOfRangeException('Language '.$lang.' is not accepted.');
            }
            
            $this -> locale = $this -> setLocales($this -> accepted[$lang]);
        } else {
            $this -> locale = $lang;
        }
        
        $this -> formatter -> setLocale($this -> locale);
        $this -> lang = $lang;
        
        return $this;
    }
    
    /**
     * Set locales.
     * 
     * @param string|array
     * @return string
     */
    protected function setLocales($locales)
    {
        $locales = (array) $locales;
        
        foreach ($locales as $key => $locale) {
            if (!$key) {
                $key = LC_ALL;
            }
            
            setlocale($key, $locale);
        }
        
        return reset($locales);
    }
    
    /**
     * Set current (accepted) lang as best one from array.
     * Example array:
     *   pl => 1
     *   en => 0.8
     * 
     * Method works only, if list of accepted languages is set.
     * 
     * @return false|string Selected language or false
     */
    public function chooseLang(array $langs)
    {
        foreach ($langs as $lang => $q) {
            if (isset($this -> accepted[$lang])) {
                $this -> setLang($lang);
                return $lang;
            }
        }
        
        return false;
    }
    
    /**
     * Get current language.
     * 
     * @return string
     */
    public function getLang()
    {
        return $this -> lang;
    }
    
    /**
     * Set current domain.
     * 
     * @param string
     * @return self
     */
    public function setDomain($domain)
    {
        $this -> domain = $domain;
        return $this;
    }
    
    /**
     * Get current domain.
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this -> domain;
    }
    
    /**
     * Push domain on stack.
     * 
     * Example:
     * $i18n -> inDomain('news');
     * 
     * $str = $i18n -> get('title')
     * 
     * $i18n -> outDomian();
     * 
     * @param string
     * @return self
     */
    public function inDomain($domain)
    {
        array_push($this -> domainStack, $this -> domain);
        $this -> setDomain($domain);
        return $this;
    }
    
    /**
     * Pop domain from stack.
     * 
     * @see inDomain()
     * @return self
     */
    public function outDomain()
    {
        if ($this -> domainStack) {
            $this -> setDomain(array_pop($this -> domainStack));
        }
        
        return $this;
    }
    
    /**
     * Get string form "global" domain.
     * 
     * @param string|Translatable
     * @param mixed
     * @see get()
     */
    public function getGlobal($id, $args = [])
    {
        return $this -> get($id, $args, 'global');
    }
    
    /**
     * Get string form specified domain.
     * 
     * @param string|Translatable
     * @param mixed
     * @param string
     */
    public function get($id, $args = [], $domain = null)
    {
        if (is_array($id)) {
            return array_map(
                function ($v) use ($args, $domain) {
                    return $this->get($v, $args, $domain);
                },
                $id
            );
        }
        
        if (!$domain) {
            $domain = $this -> domain;
        }
        
        if ($id instanceof Translatable) {
            return $id -> translate($this);
        }
        
        $str = $this -> backend -> get($this -> lang, $domain, $id);
        
        if ($str === null) {
            $str = $this -> backend -> get($this -> defaultLang, $domain, $id);
        }
        
        if ($str === null) {
            $str = 'i18n('.$domain.'): '.$id;
        }
        
        $args = (array) $args;
        
        if (!empty($args)) {
            return $this -> formatter -> string($str, $args);
        }
        
        return $str;
    }
}
