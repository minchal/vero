<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Exception;

use Vero\Application\TranslatableException;

/**
 * Exception to report bad action call.
 */
class NotFound extends TranslatableException
{
    protected $url;
    
    public static function url($url)
    {
        return new self('not found', 'global', [], $url);
    }
    
    public function __construct($msg, $domain = null, $params = [], $url = null)
    {
        $this -> url = $url;
        parent::__construct($msg, $domain, $params);
    }
    
    public function getUrl()
    {
        return $this -> url;
    }
}
