<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

use Vero\I18n\Translator;
use Vero\I18n\Translatable;
use Vero\Application\Exception;

/**
 * Validating error message.
 * Message can be translated by appropriate service.
 */
class Error implements Translatable
{
    protected $id;
    protected $domain;
    protected $args = [];
    protected $i18n;
    protected $translate;
    
    /**
     * Create translated error.
     * 
     * @param string
     * @return self
     */
    public static function raw($msg)
    {
        return new self($msg, null, null, false);
    }
    
    /**
     * Create translatable error.
     * 
     * @param string
     * @param string
     * @param array
     * @return self
     */
    public static function create($id, $args = array(), $domain = null)
    {
        return new self($id, $args, $domain);
    }
    
    /**
     * Create from translatable exception.
     * 
     * @return self
     */
    public static function exc(Exception $exc)
    {
        return new self($exc->getMsgId(), $exc->getMsgParams(), $exc->getMsgDomain());
    }
    
    /**
     * Use static constructors instead.
     */
    protected function __construct($id, $args = [], $domain = null, $translate = true)
    {
        $this -> id   = $id;
        $this -> args = $args;
        $this -> domain = $domain;
        $this -> translate = $translate;
    }
    
    /**
     * Get translation id or message.
     * 
     * @return string
     */
    public function getId()
    {
        return $this -> id;
    }
    
    /**
     * Get translation arguments.
     * 
     * @return array
     */
    public function getArgs()
    {
        return $this -> args;
    }
    
    /**
     * {@inheritdoc}
     */
    public function translate(Translator $i18n)
    {
        $this -> i18n = $i18n;
        return (string) $this;
    }
    
    /**
     * Get error message as string (if posible, translated).
     * 
     * @return string
     */
    public function __toString()
    {
        if ($this -> i18n && $this -> translate) {
            if ($this -> domain) {
                return $this -> i18n -> get($this->id, $this->args, $this -> domain);
            }
            
            return $this -> i18n -> getGlobal('validator '.$this->id, $this->args);
        }
        
        return $this -> id;
    }
}
