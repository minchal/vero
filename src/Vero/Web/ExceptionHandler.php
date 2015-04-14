<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\I18n\Translatable;
use Vero\Web\Request;
use Vero\Web\Exception;

/**
 * Standard Application/User exception handler for Vero\Web\Controller.
 * It should handle only exception, which message can be shown to user.
 */
class ExceptionHandler extends Action
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
        
        $response = $this -> response($this -> getExceptionResponseBody(($e)));
        
        if ($e instanceof Exception\NotFound) {
            $response -> headerCode(404);
        } elseif ($e instanceof Exception\AccessDenied) {
            $response -> headerCode(403);
        } else {
            $response -> headerCode(400);
        }
        
        return $response;
    }
    
    /**
     * Get instance of ResponseBody for exception.
     * 
     * @return string|\Vero\Web\ResponseBody
     * @api
     */
    protected function getExceptionResponseBody(\Exception $e)
    {
        return $this -> render(
            $this -> getExceptionTemplate($e),
            [
                'exception' => $e,
                'message'   => $e -> getMessage()
            ]
        );
    }
    
    /**
     * Get template file name for exception.
     * 
     * @return string
     * @api
     */
    protected function getExceptionTemplate(\Exception $e)
    {
        return 'exception/exception.twig';
    }
    
    /**
     * Add info about exception to logger, if posible.
     * 
     * @return boolean
     * @api
     */
    protected function logException(\Exception $e)
    {
        if (!$this -> getContainer() -> has('log')) {
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
