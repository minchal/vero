<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Exception;

use Vero\Application\Exception;

/**
 * Exception to report bad action call.
 */
class NotFound extends Exception
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
