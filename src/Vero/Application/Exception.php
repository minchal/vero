<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Application;

use Vero\I18n\Translator;
use Vero\I18n\Translatable;

/**
 * Basic not-critical application exception.
 */
class Exception extends \RuntimeException implements Translatable
{
    protected $msgId;
    protected $msgDomain;
    protected $msgParams;
    
    public function __construct($msg, $domain = null, $params = [])
    {
        $this -> msgId = $msg;
        $this -> msgDomain = $domain;
        $this -> msgParams = $params;
        
        parent::__construct($msg);
    }
    
    public function getMsgId()
    {
        return $this -> msgId;
    }
    
    public function getMsgDomain()
    {
        return $this -> msgDomain;
    }
    
    public function getMsgParams()
    {
        return $this -> msgParams;
    }
    
    /**
     * {@inheritdoc}
     */
    public function translate(Translator $i18n)
    {
        return $this -> message = $i18n -> get($this -> msgId, $this -> msgParams, $this -> msgDomain);
    }
}
