<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Action;

use Vero\I18n\Translatable;
use Vero\Web\Request;
use Vero\Web\Exception;

/**
 * Standard Application/User exception handler for Vero\Web\Controller.
 * It should handle only exception, which message can be shown to user.
 */
class ExceptionHandler extends Session
{
    /**
     * {@inheritdoc}
     */
    public function run(Request $req)
    {
        $e = $req -> param('exception');
        
        if (!$e instanceof \Exception) {
            throw new \BadMethodCallException('ExceptionHandler Action needs "exception" param in Request!');
        }
        
        if ($e instanceof Translatable) {
            $e -> translate($this -> get('i18n'));
        }
        
        $this -> logException($e);
        
        $response = $this -> response($this -> render($this -> getTemplate($e), ['message' => $e -> getMessage()]));
        
        if ($e instanceof Exception\NotFound) {
            $response -> headerCode(404);
        }
        
        if ($e instanceof Exception\AccessDenied) {
            $response -> headerCode(403);
        }
        
        return $response;
    }
    
    /**
     * Get template file name for exception.
     * 
     * @return string
     */
    protected function getTemplate(\Exception $e)
    {
        return 'exception/exception.twig';
    }
    
    /**
     * Add info about exception to logger, if posible.
     * 
     * @return boolean
     */
    protected function logException(\Exception $e)
    {
        if (!$this -> container -> has('log')) {
            return false;
        }
        
        $lvl = 'notice';
        $msg = "Application Exception [{exception}]: '{message}'\n    from {file}:{line}";
        $ctx = [
            'exception' => get_class($e),
            'message' => $e -> getMessage(),
            'file' => $e -> getFile(),
            'line' => $e -> getLine()
        ];
        
        if ($e instanceof Exception\AccessDenied) {
            $msg .= "\n    Resource: {resource}";
            $lvl = 'warning';
            $ctx['resource'] = $e -> getResource();
        }
        
        if ($e instanceof Exception\NotFound) {
            $msg .= "\n    URL: {url}";
            $ctx['url'] = $e -> getUrl();
        }
        
        $this -> get('log') -> $lvl($msg, $ctx);
        
        return true;
    }
}
