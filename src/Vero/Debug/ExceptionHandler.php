<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Debug;

use Vero\Config\Config;
use Vero\DependencyInjection\Container;

/**
 * Exception handler.
 */
class ExceptionHandler
{
    protected $view;
    protected $data = [];
    protected $sensitive = [];
    protected $listeners = [];
    
    /**
     * Create exception handler.
     * Instance of config should be this same as for application.
     */
    public function __construct(Config $config, $view)
    {
        $this -> view = $view;
        
        $this -> data['debug']    = $config -> get('debug');
        $this -> data['project']  = $config -> get('app.project', 'Vero');
        $this -> data['admin']    = $config -> get('app.admin');
        $this -> data['basepath'] = $config -> get('routing.base', '/');
        
        $this -> sensitive = [
            $config -> get('salt'),
            $config -> get('database.password')
        ];
    }
    
    /**
     * Add listener.
     * Listener should expect one argument, array with keys:
     *  exception, message, file, line, trace, buffer
     * 
     * @param callable
     * @return self
     */
    public function addListener(callable $listener)
    {
        $this -> listeners[] = $listener;
        return $this;
    }
    
    /**
     * Add PSR-3 Log listener form DI Container.
     * 
     * @see \Vero\Log\Logger
     * @param Container
     * @param string
     * @return self
     */
    public function addLogListener(Container $container, $key = 'log', $level = 'error')
    {
        $this -> addListener(
            function ($data) use ($container, $key, $level) {
                $container -> get($key) -> $level("Exception [{exception}]: '{message}'\n    in {file}:{line}", $data);
            }
        );
        
        return $this;
    }
    
    /**
     * Register this handler.
     */
    public function register()
    {
        set_exception_handler(array($this, 'handle'));
    }
    
    /**
     * Unregister this handler.
     */
    public function unregister()
    {
        set_exception_handler(null);
    }
    
    /**
     * Handle exception.
     */
    public function handle(\Exception $exception)
    {
        $data = $this->data;
        
        $data['exception']= get_class($exception);
        $data['message']  = $exception -> getMessage();
        $data['file']     = $exception -> getFile();
        $data['line']     = $exception -> getLine();
        $data['trace']    = $exception -> getTraceAsString();
        $data['buffer']   = ob_get_clean();
        $data['request']  = isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST']) ?
            $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : '';
        
        if (strlen($data['file']) > 50) {
            $data['file'] = '...'.substr($data['file'], -50);
        }
        
        foreach ($this -> sensitive as $sensitive) {
            // in StackTrace passed arguments have only 15 letters
            foreach ([$sensitive, substr($sensitive, 0, 15)] as $s) {
                $data['message'] = str_replace($s, str_repeat('*', 15), $data['message']);
                $data['trace']   = str_replace($s, str_repeat('*', 15), $data['trace']);
            }
        }
        
        foreach ($this -> listeners as $listener) {
            try {
                $listener($data);
            } catch (\Exception $e) {
                // ignore listener exceptions
            }
        }
        
        if (PHP_SAPI === 'cli') {
            $this -> displayCli($data);
        } else {
            $this -> displayHtml($data);
        }
    }
    
    protected function displayHtml(array $vars)
    {
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/html; charset=UTF-8');
        }
        
        extract($vars);
        
        require $this -> view;
        exit();
    }
    
    protected function displayCli(array $vars)
    {
        extract($vars);
        
        // white on red
        fwrite(
            STDERR,
            "\n\033[1;37m\033[41m\n".
            "   Exception occured [$exception]:\n".
            "      $message\n".
            ($debug ? "\n   In: $file:$line\n" : "").
            "\033[0m\n\n"
        );
    }
}
