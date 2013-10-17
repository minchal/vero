<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Exception;

use Vero\Application\Exception;

/**
 * Exception to report bad action call.
 */
class AccessDenied extends Exception
{
    protected $resource;
    
    public static function resource($resource)
    {
        return new self('access denied', 'global', [], $resource);
    }
    
    public function __construct($msg, $domain = null, $params = [], $resource = null)
    {
        $this -> resource = $resource;
        parent::__construct($msg, $domain, $params);
    }
    
    public function getResource()
    {
        return $this -> resource;
    }
}
