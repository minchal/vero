<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Factory implementation with callback/anonymous function.
 */
class CallbackFactory extends Factory
{
    protected $callback;
    
    /**
     * Create factory instance by callback.
     * 
     * DI Container is first argument for callback.
     * Callback can have more arguments.
     */
    public function __construct(callable $callback)
    {
        $this -> callback = $callback;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $args = func_get_args();
        array_unshift($args, $this -> container);
        
        return call_user_func_array($this -> callback, $args);
    }
}
