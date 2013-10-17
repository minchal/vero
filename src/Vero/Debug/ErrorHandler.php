<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Debug;

use ErrorException;

/**
 * PHP Error handler.
 */
class ErrorHandler
{
    private $levels = [
        E_WARNING           => 'Warning',
        E_NOTICE            => 'Notice',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
    ];
    
    /**
     * Register this handler.
     */
    public function register()
    {
        set_error_handler(array($this, 'handle'));
    }
    
    /**
     * Unregister this handler.
     */
    public function unregister()
    {
        set_error_handler(null);
    }
    
    /**
     * Handle PHP error.
     */
    public function handle($level, $message, $file, $line)
    {
        if (error_reporting() & $level) {
            $lvl = isset($this->levels[$level]) ? $this->levels[$level] : $level;
            throw new ErrorException("$lvl: $message in $file:$line", 0, $level, $file, $line);
        }
        
        return false;
    }
}
